<!DOCTYPE html>
<html lang="de">
<head>
    <style>
        @page {
            margin: 0;
            margin-top: 4mm;
        }
        .label {
            border: solid 1px white; width: 61.4mm; height:28.3mm;
            vertical-align: bottom;
            padding: 4mm;
            float: left;
        }
    </style>
</head>
<body>
    <div style="margin-top:4mm;">
        @for($i=0; $i<$skipLabels; $i++)
        <div class="label">&nbsp;</div>
        @endfor
        @foreach($books as $book)
            <div class="label">
                <table style="width: 100%;">
                    <tr>
                        <td valign="bottom">
                            <div style="font-size: .5em;">{{ $book->authors[0] ? $book->authors[0]->name : '' }}</div>
                            <div style="font-size: .5em; font-weight: bold; font-style: italic; padding-bottom: 4mm;">{{ $book->title }}<br />&nbsp;</div>
                            @if($book->ISBN)
                                <div style="padding-top: 4mm;"><barcode code="{{ (strlen($book->ISBN) > 10) ? $book->ISBN : \Biblys\Isbn\Isbn::convertToEan13($book->ISBN) }}" type="ISBN" text="1" height="0.3"/></div>
                            @endif
                        </td>
                        <td style="padding-left: 0.5cm;" valign="top">
                            {!!  str_replace('.', "<br />.", $book->ddc) !!}<br />
                            {{ $book->authors[0] ? strtoupper(substr($book->authors[0]->last_name, 0, 4)) : '' }}
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
            @for($i=0; $i<(24-count($books)-$skipLabels); $i++)
                <div class="label">&nbsp;</div>
            @endfor
    </div>
</body>
</html>
