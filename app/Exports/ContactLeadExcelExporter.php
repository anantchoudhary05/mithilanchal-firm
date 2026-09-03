<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\ContactLead;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

final class ContactLeadExcelExporter
{
    /**
     * @var list<string>
     */
    private const HEADERS = [
        'S.No.',
        'Name',
        'Company',
        'Email',
        'Phone',
        'Requirement',
        'Quantity',
        'Message',
        'Status',
        'Admin notes',
        'Received',
    ];

    /**
     * @var array<string, string>
     */
    private const SHEETS = [
        'New' => ContactLead::STATUS_NEW,
        'Contacted' => ContactLead::STATUS_CONTACTED,
        'Closed' => ContactLead::STATUS_CLOSED,
    ];

    public function download(?string $filename = null): BinaryFileResponse
    {
        $path = $this->writeWorkbook();
        $filename ??= 'contact-enquiries-'.now()->format('Y-m-d').'.xlsx';

        return response()
            ->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }

    public function writeWorkbook(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'leads-xlsx-') ?: sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('leads-xlsx-', true);
        @unlink($path);
        $path .= '.xlsx';

        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create the Excel report.');
        }

        $sheetFiles = [];
        $sheetIndex = 1;

        foreach (self::SHEETS as $title => $status) {
            $sheetFiles[] = 'worksheets/sheet'.$sheetIndex.'.xml';
            $zip->addFromString(
                'xl/worksheets/sheet'.$sheetIndex.'.xml',
                $this->worksheetXml($this->rowsForStatus($status)),
            );
            $sheetIndex++;
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml($sheetFiles));
        $zip->addFromString('_rels/.rels', $this->packageRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml(array_keys(self::SHEETS)));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml(count($sheetFiles)));
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->close();

        return $path;
    }

    /**
     * @return list<list<int|string>>
     */
    private function rowsForStatus(string $status): array
    {
        /** @var Collection<int, ContactLead> $leads */
        $leads = ContactLead::query()
            ->where('status', $status)
            ->latest()
            ->get();

        return $leads->values()->map(function (ContactLead $lead, int $index): array {
            return [
                $index + 1,
                $lead->name ?: '',
                $lead->company ?: '',
                $lead->email ?: '',
                $lead->phone ?: '',
                $lead->requirement ?: '',
                $lead->quantity ?: '',
                $lead->message ?: '',
                $lead->statusLabel(),
                $lead->admin_notes ?: '',
                $lead->receivedLabel() === '—' ? '' : $lead->receivedLabel(),
            ];
        })->all();
    }

    /**
     * @param  list<list<int|string>>  $rows
     */
    private function worksheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData>';

        $xml .= $this->rowXml(1, self::HEADERS, header: true);

        foreach ($rows as $index => $row) {
            $xml .= $this->rowXml($index + 2, $row);
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    /**
     * @param  list<int|string>  $values
     */
    private function rowXml(int $rowNumber, array $values, bool $header = false): string
    {
        $cells = '';

        foreach (array_values($values) as $column => $value) {
            $ref = $this->columnLetter($column).$rowNumber;

            if (is_int($value) || (is_string($value) && $value !== '' && ctype_digit($value) && $column === 0)) {
                $style = $header ? ' s="1"' : '';
                $cells .= '<c r="'.$ref.'"'.$style.' t="n"><v>'.(int) $value.'</v></c>';

                continue;
            }

            $style = $header ? ' s="1"' : '';
            $cells .= '<c r="'.$ref.'"'.$style.' t="inlineStr"><is><t xml:space="preserve">'.$this->escape((string) $value).'</t></is></c>';
        }

        return '<row r="'.$rowNumber.'">'.$cells.'</row>';
    }

    private function columnLetter(int $zeroIndex): string
    {
        $letter = '';
        $index = $zeroIndex + 1;

        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intdiv($index - 1, 26);
        }

        return $letter;
    }

    private function escape(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value) ?? $value;

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param  list<string>  $sheetNames
     */
    private function workbookXml(array $sheetNames): string
    {
        $sheets = '';

        foreach (array_values($sheetNames) as $index => $name) {
            $id = $index + 1;
            $sheets .= '<sheet name="'.$this->escape($name).'" sheetId="'.$id.'" r:id="rId'.$id.'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'.$sheets.'</sheets>'
            .'</workbook>';
    }

    private function workbookRelsXml(int $sheetCount): string
    {
        $rels = '';

        for ($i = 1; $i <= $sheetCount; $i++) {
            $rels .= '<Relationship Id="rId'.$i.'"'
                .' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"'
                .' Target="worksheets/sheet'.$i.'.xml"/>';
        }

        $rels .= '<Relationship Id="rId'.($sheetCount + 1).'"'
            .' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"'
            .' Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .$rels
            .'</Relationships>';
    }

    /**
     * @param  list<string>  $sheetFiles
     */
    private function contentTypesXml(array $sheetFiles): string
    {
        $overrides = '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';

        foreach ($sheetFiles as $file) {
            $overrides .= '<Override PartName="/xl/'.$file.'" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .$overrides
            .'</Types>';
    }

    private function packageRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2">'
            .'<font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="2">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'</fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="2">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'</cellXfs>'
            .'</styleSheet>';
    }
}
