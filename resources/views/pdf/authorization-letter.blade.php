<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 95px 48px 70px 48px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.42;
        }
        .letterhead {
            position: fixed;
            top: -78px;
            left: 0;
            right: 0;
            height: 70px;
            text-align: center;
        }
        .letterhead-banner {
            width: 100%;
            max-height: 68px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .line {
            border-bottom: 1.2px solid #222;
            margin-top: 0;
        }
        .footer-fixed {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #222;
            padding-top: 6px;
            color: #222;
        }
        .watermark {
            position: fixed;
            top: 36%;
            left: 18%;
            width: 64%;
            opacity: 0.07;
            z-index: -1;
        }
        .watermark img { width: 100%; }
        .meta-table { width: 100%; margin: 2px 0 12px; }
        .meta-table td { vertical-align: top; font-size: 11px; }
        .meta-right { text-align: right; white-space: nowrap; }
        .to-block { font-weight: bold; margin: 8px 0 12px; line-height: 1.55; }
        .subject {
            font-weight: bold;
            color: #0033a0;
            text-decoration: underline;
            margin: 0 0 12px;
            text-align: justify;
        }
        p { margin: 0 0 8px; text-align: justify; }
        .indent { text-indent: 22px; }
        ol {
            margin: 4px 0 10px 18px;
            padding: 0;
        }
        ol li { margin-bottom: 5px; text-align: justify; }
        table.trainers {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px;
        }
        table.trainers th, table.trainers td {
            border: 1px solid #222;
            padding: 7px 6px;
            text-align: center;
            font-size: 11px;
        }
        table.trainers th { font-weight: bold; }
        .after-table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
        }
        .after-table td { vertical-align: top; }
        .close-block { margin-top: 0; line-height: 1.45; }
        .sign-wrap { text-align: right; padding-top: 0; }
        .sign-img { height: 78px; }
    </style>
</head>
<body>
    <div class="letterhead">
        @if(!empty($letterheadPath))
            <img class="letterhead-banner" src="{{ $letterheadPath }}" alt="SOPL Letterhead">
        @elseif(!empty($logoPath))
            <img class="letterhead-banner" src="{{ $logoPath }}" alt="SOPL">
        @endif
        <div class="line"></div>
    </div>

    <div class="footer-fixed">
        REGISTERED OFFICE: PLOT NO-1634, SECTOR-82, INDUSTRIAL AREA MOHALI, PUNJAB
    </div>

    @if(!empty($logoPath))
        <div class="watermark"><img src="{{ $logoPath }}" alt=""></div>
    @endif

    <table class="meta-table">
        <tr>
            <td><strong>Ref. No.</strong> {{ $refNo }}</td>
            <td class="meta-right"><strong>Date:</strong> {{ $letterDate }}</td>
        </tr>
    </table>

    <div class="to-block">
        To<br>
        The Principal<br>
        {{ $schoolName }}<br>
        {{ $district }}
    </div>

    <div class="subject">
        Subject: Conduct of Rani Laxmi Bai Atam Raksha Prashikshan (Self-Defence Training and Awareness Programme)
        for Girl Students of GMSs/GHSs/GSSSs/PM SHRI Schools in {{ $stateName }}
    </div>

    <p>Sir/Madam,</p>

    <p class="indent">
        This is with reference to the Memorandum of Understanding (MoU) for the academic session 2026–27 executed
        between Samagra Shiksha, {{ $stateName }}, and Sane Overseas Private Limited
        for the implementation of the Rani Laxmi Bai Atam Raksha Prashikshan (Self-Defence Training and Awareness Programme)
        for girl students studying in Government Middle Schools (GMSs), Government High Schools (GHSs),
        Government Senior Secondary Schools (GSSSs), and PM SHRI Schools across {{ $stateName }}.
    </p>

    <p class="indent">
        Under this programme, Sane Overseas Private Limited, Mohali, the agency empaneled by Samagra Shiksha, {{ $stateName }},
        has been entrusted with conducting self-defence training and awareness sessions for girl students of Classes VI to XII.
    </p>

    <p>
        You are, therefore, requested to extend your cooperation in the successful implementation of this programme by ensuring the following:
    </p>
    <ol>
        <li>Make suitable time available for the training programme during regular school hours.</li>
        <li>Encourage and ensure the participation of all eligible girl students (Classes VI to XII).</li>
        <li>Ensure that the Physical Education Teacher and one Lady Teacher remain present throughout the training sessions for proper coordination and supervision.</li>
        <li>Extend all necessary support to facilitate the smooth conduct of the programme during the academic session.</li>
    </ol>

    <p>
        You are further requested to permit the following authorised trainer(s) deputed by Sane Overseas Private Limited
        to conduct the training programme in your school.
    </p>

    <p>
        Your cooperation in the effective implementation of this important initiative aimed at empowering and ensuring
        the safety of girl students will be highly appreciated.
    </p>

    <table class="trainers">
        <thead>
            <tr>
                <th style="width:12%;">S. No.</th>
                <th style="width:32%;">Trainer Name</th>
                <th style="width:28%;">Mobile No.</th>
                <th style="width:28%;">Trainer Code</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $trainerName }}</td>
                <td>{{ $mobile }}</td>
                <td>{{ $trainerCode }}</td>
            </tr>
        </tbody>
    </table>

    <table class="after-table">
        <tr>
            <td style="width:55%;" class="close-block">
                <div>Thanking you.</div>
            </td>
            <td style="width:45%;" class="sign-wrap">
                @if(!empty($signPath))
                    <img class="sign-img" src="{{ $signPath }}" alt="Authorized Signatory">
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
