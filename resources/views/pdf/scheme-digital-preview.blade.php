<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        /* ===== GOVT HEADER ===== */
        .gov-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .gov-header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .gov-header h3 {
            margin: 4px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }

        /* ===== SECTION ===== */
        .section {
            page-break-before: always;
        }

        .section:first-child {
            page-break-before: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
            padding-bottom: 4px;
        }

        /* ===== FIELD TABLE ===== */
        table.form-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.form-table td {
            padding: 6px 4px;
            vertical-align: bottom;
        }

        .label {
            width: 40%;
            font-weight: bold;
        }

        .value {
            width: 60%;
            border-bottom: 1px dotted #000;
            height: 18px;
        }

        /* ===== TWO COLUMN ===== */
        .half {
            width: 50%;
        }

        /* ===== CHECKBOX ===== */
        .checkbox {
            margin-bottom: 6px;
        }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    {{-- ===== HEADER ===== --}}
    <div class="gov-header">
        <h2>Government of West Bengal</h2>
        <h3>{{ $scheme->scheme_name }}</h3>
    </div>

    {{-- ===== LOOP TABS ===== --}}
    @foreach($tabs as $tab)

        <div class="section">

            <div class="section-title">
                {{ $tab->masterTab->tab_name }}
            </div>

            {{-- ===== TAB 104 : ENCLOSURE ===== --}}
            @if($tab->tab_code == 104)

                @foreach($attachedDocuments as $doc)
                    <div class="checkbox">
                        ☐ {{ $doc->docType->name }}
                    </div>
                @endforeach

                {{-- ===== TAB 105 : SELF DECLARATION ===== --}}
            @elseif($tab->tab_code == 105)

                <table class="form-table">
                    @foreach($selfDeclarationDisplay as $row)
                        <tr>
                            <td class="label">
                                {{ $row['field']->level_name }}
                            </td>
                            <td class="value">
                                &nbsp;
                            </td>
                        </tr>
                    @endforeach
                </table>

                {{-- ===== NORMAL TABS ===== --}}
            @else

                <table class="form-table">
                    @foreach($digitalPreviewFields[$tab->tab_code] ?? [] as $index => $field)

                        @if($index % 2 == 0)
                            <tr>
                        @endif

                            <td class="label half">
                                {{ strip_tags($field->level_name) }}
                            </td>
                            <td class="value half">
                                &nbsp;
                            </td>

                            @if($index % 2 == 1)
                                </tr>
                            @endif

                    @endforeach
                </table>

            @endif

        </div>
    @endforeach

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        This is a system generated form. No signature required.
    </div>

</body>

</html>
