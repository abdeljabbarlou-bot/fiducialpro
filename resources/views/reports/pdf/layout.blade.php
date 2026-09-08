<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('doc_title', 'Rapport') - {{ config('cabinet.name') }}</title>
    <style>
        @page { margin: 90px 30px 60px 30px; }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #334155;
        }

        /* --- En-tête répété sur chaque page --- */
        .doc-header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 62px;
            border-bottom: 2px solid #0f172a;
        }
        .doc-header .brand-name {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.3px;
        }
        .doc-header .legal-line {
            font-size: 7.5px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .doc-header .report-name {
            font-size: 9.5px;
            color: #0284c7;
            font-weight: bold;
            margin-top: 3px;
        }
        .doc-header .meta {
            font-size: 8.5px;
            color: #64748b;
            text-align: right;
        }

        /* --- Pied de page répété --- */
        .doc-footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 7.5px;
            color: #94a3b8;
        }
        .doc-footer .page-num:after {
            content: "Page " counter(page) " / " counter(pages);
        }

        /* --- Encadré de synthèse --- */
        .summary-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #0284c7;
            padding: 9px 12px;
            margin-bottom: 6px;
        }
        .summary-box td {
            border: none;
            padding: 2px 6px;
            font-size: 9px;
            color: #475569;
        }
        .summary-box strong { color: #0f172a; font-size: 10px; }

        /* --- Tableau de données --- */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        table.data th {
            background: #0f172a;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.data td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }
        table.data tr.totals td {
            background: #eff6ff;
            border-top: 2px solid #0f172a;
            border-bottom: none;
            font-weight: bold;
            color: #0f172a;
            font-size: 9.5px;
            padding: 8px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; }
        .muted { color: #94a3b8; }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            font-style: italic;
            border: 1px dashed #cbd5e1;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="doc-header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; width: 62%; border: none; padding: 0;">
                    @if(file_exists(public_path('images/logo.jpg')))
                        <div style="background: #0f172a; display: inline-block; padding: 3px 7px; border-radius: 3px;">
                            <img src="{{ public_path('images/logo.jpg') }}" style="height: 22px; width: auto; display: block;">
                        </div>
                    @else
                        <div class="brand-name">{{ mb_strtoupper(config('cabinet.name')) }}</div>
                    @endif
                    <div class="legal-line">
                        {{ config('cabinet.tagline') }} &bull; ICE : {{ config('cabinet.ice') }} &bull; IF : {{ config('cabinet.if') }} &bull; RC : {{ config('cabinet.rc') }}
                    </div>
                    <div class="report-name">@yield('report_name')</div>
                </td>
                <td style="vertical-align: top; width: 38%; border: none; padding: 0;" class="meta">
                    @yield('report_meta')
                    <div style="margin-top: 3px;">
                        Édité le <strong>{{ now()->format('d/m/Y à H:i') }}</strong><br>
                        Par {{ auth()->user()->name ?? 'Système' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-footer">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="border: none; padding: 0; font-size: 7.5px; color: #94a3b8;">
                    {{ config('cabinet.name') }} &bull; {{ config('cabinet.footer_mention') }} &bull; {{ config('cabinet.city') }}, {{ config('cabinet.country') }}
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <span class="page-num"></span>
                </td>
            </tr>
        </table>
    </div>

    <main>
        @yield('content')
    </main>

</body>
</html>
