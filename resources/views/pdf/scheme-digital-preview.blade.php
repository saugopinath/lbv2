<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
        }

        .section {
            page-break-before: always;
        }

        .field {
            width: 48%;
            display: inline-block;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Government of West Bengal</h2>
        <h3>{{ $scheme->scheme_name }}</h3>
    </div>

    @foreach($tabs as $tab)
        <div class="section">
            <h3>{{ $tab->masterTab->tab_name }}</h3>

            @if($tab->tab_code == 104)
                @foreach($attachedDocuments as $doc)
                    ☐ {{ $doc->docType->name }} <br>
                @endforeach

            @elseif($tab->tab_code == 105)
                @foreach($selfDeclarationDisplay as $row)
                    {{ $row['field']->level_name }} : __________ <br>
                @endforeach

            @else
                @foreach($digitalPreviewFields[$tab->tab_code] ?? [] as $field)
                    <div class="field">
                        {{ strip_tags($field->level_name) }} : __________
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach

</body>

</html>
