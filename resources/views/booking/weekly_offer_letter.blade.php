<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Zamar Valley – Possession Slip</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: #c8c8c8;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    font-family: 'Segoe UI', Calibri, 'Helvetica Neue', Arial, sans-serif;
  }

  /* ── A4 SLIP CARD ── */
  .slip {
    width: 210mm;
    height: 148mm;  /* A5 landscape feel – matches the receipt proportion */
    background: #ffffff;
    position: relative;
    padding: 18mm 16mm 14mm 16mm;
    box-shadow: 0 8px 40px rgba(0,0,0,0.25);
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto;
    gap: 0;
    overflow: hidden;
  }

  /* ── WATERMARK CIRCLE ── */
  .slip::before {
    content: '';
    position: absolute;
    top: -60mm;
    left: -30mm;
    width: 160mm;
    height: 160mm;
    border-radius: 50%;
    border: 18mm solid rgba(180,200,160,0.18);
    pointer-events: none;
    z-index: 0;
  }
  .slip::after {
    content: '';
    position: absolute;
    top: -30mm;
    left: -10mm;
    width: 110mm;
    height: 110mm;
    border-radius: 50%;
    border: 10mm solid rgba(180,200,160,0.12);
    pointer-events: none;
    z-index: 0;
  }

  /* ── LEFT COLUMN ── */
  .col-left {
    position: relative;
    z-index: 1;
    padding-right: 10mm;
  }

  /* LOGO */
  .logo-wrap {
    margin-bottom: 10mm;
  }
  .logo-wrap img {
    width: 52mm;
    height: auto;
    display: block;
  }

  /* FORM FIELDS */
  .field-group {
    display: flex;
    flex-direction: column;
    gap: 5.5mm;
  }

  .field-row {
    display: flex;
    align-items: flex-end;
    gap: 0;
  }

  .field-label {
    font-size: 11.5pt;
    font-weight: 500;
    color: #1a1a1a;
    white-space: nowrap;
    flex-shrink: 0;
    padding-bottom: 1px;
    letter-spacing: 0.01em;
  }

  .field-line {
    flex: 1;
    border-bottom: 1.2px solid #2a2a2a;
    margin-left: 3mm;
    height: 18px;
    min-width: 45mm;
  }

  /* Block + Size on same row */
  .field-row-inline {
    display: flex;
    align-items: flex-end;
    gap: 1mm;
  }
  .field-row-inline .field-line-short {
    border-bottom: 1.2px solid #2a2a2a;
    width: 37mm;
    height: 18px;
    margin-left: 2mm;
  }

  /* Signature – extra space above */
  .sig-row {
    margin-top: 10mm;
  }

  /* ── RIGHT COLUMN ── */
  .col-right {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: space-between;
  }

  /* POSSESSION SLIP BANNER */
  .possession-banner {
    background: linear-gradient(135deg, #8a8a8a 0%, #5a5a5a 40%, #7a7a7a 70%, #9a9a9a 100%);
    color: #ffffff;
    font-size: 13pt;
    font-weight: 600;
    letter-spacing: 0.12em;
    padding: 7px 22px 7px 22px;
    text-transform: uppercase;
    width: 78mm;
    text-align: center;
    border-radius: 2px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), inset 0 -1px 0 rgba(0,0,0,0.2);
    position: relative;
    /* Metallic sheen */
    background-image: repeating-linear-gradient(
      90deg,
      transparent,
      transparent 2px,
      rgba(255,255,255,0.04) 2px,
      rgba(255,255,255,0.04) 4px
    ),
    linear-gradient(160deg,
      #aaaaaa 0%,
      #666666 30%,
      #888888 55%,
      #555555 75%,
      #999999 100%
    );
  }

  /* CONTACT CARD */
  .contact-card {
    background-image: repeating-linear-gradient(
      90deg,
      transparent,
      transparent 2px,
      rgba(255,255,255,0.03) 2px,
      rgba(255,255,255,0.03) 4px
    ),
    linear-gradient(160deg,
      #aaaaaa 0%,
      #666666 30%,
      #888888 55%,
      #555555 75%,
      #999999 100%
    );
    color: #ffffff;
    padding: 9px 18px;
    width: 68mm;
    border-radius: 2px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), inset 0 -1px 0 rgba(0,0,0,0.2);
  }
  .contact-card .phone {
    font-size: 13pt;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: #ffffff;
    display: block;
    margin-bottom: 3px;
  }
  .contact-card .name {
    font-size: 11.5pt;
    font-weight: 500;
    color: #f0f0f0;
    display: block;
    letter-spacing: 0.02em;
  }

  /* ── TOOLBAR ── */
  .action-toolbar {
    position: fixed;
    top: 18px;
    right: 18px;
    display: flex;
    gap: 10px;
    z-index: 999;
  }
  .action-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Segoe UI', sans-serif;
    cursor: pointer;
    border: none;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    transition: opacity .15s, transform .12s;
  }
  .action-btn:hover { opacity: .9; transform: translateY(-1px); }
  .btn-print    { background: #1e3a8a; color: #fff; }
  .btn-download { background: #dc2626; color: #fff; }

  /* ── PRINT ── */
  @media print {
    @page { size: A5 landscape; margin: 0; }
    body { background: none; min-height: unset; }
    .slip {
      width: 210mm;
      min-height: 148mm;
      box-shadow: none;
      margin: 0;
    }
    .action-toolbar { display: none; }
  }
</style>
</head>
<body>

<div class="slip">

  <!-- ════════════ LEFT COLUMN ════════════ -->
  <div class="col-left">

    <!-- LOGO (base64 SVG — dompdf compatible) -->
    <div class="logo-wrap">
      <img
        src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjQ1NTAgNDAwMCAxMTQwMCA4MzAwIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogIDxwYXRoIGZpbGw9InJnYigzNiwxNjMsNzkpIiBkPSJNIDE0MTcyLDk2NzUgQyAxNDQ2NSw5NDcxIDE1MDIzLDg2NjMgMTUyNDcsODIyMCAxNTcxNiw3MjkwIDE1ODEwLDYyMzQgMTU2NjUsNTE5NCBMIDE1NTU5LDQ2NDYgQyAxNTQ3NCw0NDU5IDE1NTMyLDQ1NzMgMTU0NTcsNDQ5NyAxNDE4OSw2MjA1IDE0NzM4LDU0MjAgMTQ4NTcsNjg4NCAxNDk4NSw4NDU2IDE0MjY1LDkxNjkgMTQxNzIsOTY3NSBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDE4MCw0Miw0MCkiIGQ9Ik0gMTI0MDYsODgyMSBDIDEyNTM5LDkzNDMgMTI0MDQsOTMyOCAxMjQ0NSw5NzcyIDEyNjUzLDk2ODEgMTI1NDcsOTg2MSAxMjYzNSw5NDk0IDEyNjcwLDkzNDYgMTI2NDksOTMwNyAxMjcxNSw5MTY5IDEyOTUwLDkyNzcgMTM1NjIsOTk0NiAxMzk1Nyw5OTgzIDEzOTUyLDk3MDkgMTM4NjEsOTY5MiAxMzQ1MSw5Mzc5IDEzMjAxLDkxODggMTMwODMsOTAwNSAxMjg2NSw4ODIyIDEzMDA4LDg3MTYgMTQ4MjksODE0NSAxMzU1Niw3NTM2IDEyMDc1LDY4MzAgMTI5MjMsODQ2MSAxMjQwNiw4ODIxIFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMTgwLDQyLDQwKSIgZD0iTSA2NDk3LDk5MTcgTCA2NjA4LDk3NDQgQyA2NzkyLDkzNTYgNjU5Niw4OTcyIDc2MTYsODg2NyA3NjEyLDkxMDIgNzUzMiw5NTgxIDc2OTQsOTc2MiA3OTYyLDkzODggNzkxMCw3OTY5IDc4MjEsNzc3NyA3Njk1LDc1MDIgNzc0NCw3ODE2IDc1ODgsNzQ3MCBMIDc1MTQsNzI3MSBDIDY5ODUsNzY1MiA3MDM3LDc4MjUgNjgwNiw4NTEzIDY2NzAsODkxOCA2NDkzLDg3OTQgNjUwMiw5MTQxIDY1MDgsOTM1NCA2NTQ0LDkyNDggNjQ4OSw5NTEyIEwgNjQ5Nyw5OTE3IFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMTgwLDQyLDQwKSIgZD0iTSAxMDYzMCw5OTI3IEMgMTEwNTEsOTIzNyAxMDUwMSw5MDM2IDExNzE0LDg4NjggTCAxMTczMSw5NzY3IEMgMTE5MjUsOTY1OCAxMTkwMiw5NzE3IDExOTUyLDkxNjMgMTE5NzcsODg3OSAxMjAzNCw4MDAyIDExOTA3LDc3NTYgMTE3NzcsNzUwNCAxMTc1OCw3NzU5IDExNjc4LDc0NDYgMTE2NzQsNzQzMSAxMTY2NSw3Mzc3IDExNjYyLDczNjIgMTE2NjAsNzM1MCAxMTY1NCw3MzI2IDExNjUyLDczMTYgMTE2NTAsNzMwNiAxMTY0OCw3Mjg0IDExNjQwLDcyNzAgMTA5MjksNzU4MCAxMTE0MSw4Mzc2IDEwNzU3LDg4MTQgMTA2MjIsODk2NiAxMDYwMyw4NzQ4IDEwNjE4LDkxMzMgMTA2NDIsOTc3NiAxMDM5OCw5Njg1IDEwNjMwLDk5MjcgWiIvPgogIDxwYXRoIGZpbGw9InJnYigxODAsNDIsNDApIiBkPSJNIDg0NzAsOTc4NyBDIDg3NTIsOTU5NSA4NzIzLDg4ODggODc2Niw4NTQ4IEwgODk0NSw4NzQ2IEMgOTM2OCw5MjEwIDk2MjEsODQ2NiA5OTUzLDgyNjkgOTkzMiw4NTE4IDk4NjgsODc2MyA5ODE4LDkwMTIgTCA5NzI1LDk1NzggQyA5NzU3LDk3ODYgOTY4OCw5NjQyIDk4MTIsOTc3NSAxMDA3NCw5MzQyIDEwMzU0LDgyODEgMTAzNTYsNzYxNCA5Nzk0LDc3ODMgOTc5OCw4NDk3IDkyNDgsODU5OCA4OTI0LDgzMDcgOTAwNyw3OTE1IDg5NTUsNzYwMiBMIDg5MTAsNzQzNyBDIDg5MDYsNzQyMyA4ODk5LDc0MDIgODg5MCw3Mzg2IDg0MTksNzQ2MyA4NTcxLDc3MTYgODQ5MSw4NDQ4IDg0NTIsODgwMyA4MzAyLDk1NDQgODQ3MCw5Nzg3IFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMjQxLDIyOSwyOCkiIGQ9Ik0gMTMxNzAsNTExOSBDIDEzMzY1LDUyMjAgMTMyOTIsNTE4OCAxMzQzNyw1MzI1IDE0MDg0LDU5MzEgMTM1ODEsNTQyMCAxNDEyMCw2MDc5IDE0Mjc0LDYyNjggMTQzNTQsNjQzNCAxNDQ0OCw2NzA0IDE0NTUwLDY5OTYgMTQ1ODUsNzMzNCAxNDY5Myw3NTAzIDE0NzcwLDY0MTUgMTQ1MzgsNTQ0NSAxMzg1Miw0Njc0IDEzNjY4LDQ0NjYgMTM1MDUsNDIzNSAxMzE3MCw0MTIxIEwgMTMxNzAsNTExOSBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDE4MCw0Miw0MCkiIGQ9Ik0gNDg2MCw3Nzk1IEMgNTE0OCw3NzU0IDU1NDUsNzY5NiA1NzY2LDc4NDggNTg2Niw3OTUwIDU3MzYsODEyNiA1NjEzLDgzMzQgTCA0NzgwLDk1NDggQyA0NjY3LDk4ODIgNDc2MCw5ODA1IDQ3NjAsOTgwNiBMIDQ3OTMsOTkxMyBDIDUxMTksOTk5OSA2MTIwLDk3MjAgNjIxMCw5MjY2IDU4MDcsOTM2MSA1NzAwLDk1MjIgNTE2OCw5NTU2IDUyOTgsODk0OSA2OTkzLDc2MzMgNTY4NCw3NDAwIDQ4NTcsNzI1MyA0ODk4LDc0OTcgNDg2MCw3Nzk1IFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMTksMjEsMjEpIiBkPSJNIDY1MDQsMTE4NDQgQyA2OTY3LDExODU3IDY3MzUsMTE4NzYgNjk3NywxMTU3MyBMIDc4MzEsMTE1NTggNzk2NCwxMTg1NiA4MzE3LDExODU3IEMgODE1OSwxMTUwNiA3NzY4LDEwNzg2IDc1NTEsMTA0OTYgNzEwMywxMDQ5NiA3MTE2LDEwNzAzIDY5MzksMTEwMzYgNjc5OCwxMTMwNCA2NjEwLDExNTg1IDY1MDQsMTE4NDQgWiIvPgogIDxwYXRoIGZpbGw9InJnYigyMTgsNTIsNDkpIiBkPSJNIDExMzUyLDQ3OTIgQyAxMTcyOSw1OTU5IDEyNjc5LDUwNDIgMTQwNzUsNjUwMSAxMzc3Niw1NjU4IDEyMDQwLDQ0NTkgMTEzNTIsNDc5MiBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDE5LDIxLDIxKSIgZD0iTSA0NzY2LDEwNTA0IEwgNTMzOCwxMTU2OSBDIDU2ODYsMTIxNTYgNTc2NiwxMTc5MiA2MDkxLDExMjAxIDYxOTYsMTEwMDggNjM4MywxMDcyOSA2NDIxLDEwNDk0IDU4MDAsMTA1MDAgNjA1MSwxMTA3OCA1NTkwLDExNDM0IDU1MDIsMTEyNDMgNTIxNywxMDY5NSA1MDgzLDEwNTEwIEwgNDc2NiwxMDUwNCBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDE5LDIxLDIxKSIgZD0iTSAxNDczMSwxMTg1NyBDIDE1MDQ4LDExODU3IDE0OTk0LDExOTIzIDE1MDU4LDExNzMxIDE1MDY0LDExNzE1IDE1MDYwLDExMjg2IDE1MDYxLDExMjI5IEwgMTU3MDQsMTA1NDIgQyAxNTI3NSwxMDM1OCAxNTE3MCwxMDgxOSAxNDkyMiwxMDk4NCAxNDg2MCwxMDkyNCAxNDgyMywxMDg5OSAxNDc1MSwxMDgzMSAxNDQ2NSwxMDU2NCAxNDU2NCwxMDQzNyAxNDA2OSwxMDUxMCAxNDEwNSwxMDUzOSAxNDM0OCwxMDg4NyAxNDUwMiwxMTAxOCAxNDgyNywxMTI5OCAxNDczNCwxMTM1MyAxNDczMSwxMTg1NyBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDE5LDIxLDIxKSIgZD0iTSA4Nzc0LDExODQ0IEwgMTAxMDcsMTE4NjAgQyAxMDA5OSwxMTUxOSAxMDE2NSwxMTU4MiA5NzMyLDExNTg3IDk1MTIsMTE1OTAgOTMyNCwxMTYwNiA5MTA0LDExNTc0IDkwNjQsMTA5OTcgOTIyNiwxMDI4OCA4ODUzLDEwNTA2IDg3MDUsMTA1OTQgODc2NSwxMDU1MSA4NzcwLDExMTMwIDg3NzIsMTEzNjggODc2OSwxMTYwNiA4Nzc0LDExODQ0IFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMTksMjEsMjEpIiBkPSJNIDEwNTY1LDExODUwIEwgMTE2NDksMTE4NjUgQyAxMjA3MSwxMTgyNyAxMTgyMCwxMTkxMCAxMTkwNiwxMTcyOSAxMTg2NCwxMTYzNiAxMTk1NSwxMTU4MCAxMTQ2MiwxMTU5MCAxMTI2NiwxMTU5MyAxMTA4OCwxMTU5OSAxMDg5MSwxMTU4MSBMIDEwODc0LDEwNDkwIDEwNTk4LDEwNTAyIEMgMTA1MTEsMTA4NDIgMTA1NTcsMTE0ODIgMTA1NjUsMTE4NTAgWiIvPgogIDxwYXRoIGZpbGw9InJnYigxODAsNDIsNDApIiBkPSJNIDEwNDI3LDU5ODkgTCAxMDY3OCw2MTIyIEMgMTExNzYsNjMwOSAxMTA5OSw2MTI5IDExNDc2LDYwMDkgMTE5NjMsNTg1NSAxMjM3OCw1OTcyIDEyODU2LDU5OTUgMTI4NDIsNTk4NiAxMjgyMSw1OTU1IDEyODE0LDU5NjcgTCAxMjM0NCw1NzU4IEMgMTE3NTIsNTU5NiAxMDkwNSw1NjE1IDEwNDI3LDU5ODkgWiIvPgogIDxwYXRoIGZpbGw9InJnYigyNywyNSwyNCkiIGQ9Ik0gMTI2NzUsMTE1NzkgQyAxMjcwOSwxMTA4MyAxMjc1MCwxMTM0NiAxMzEzMSwxMTI2MCAxMzMxMCwxMTIyMCAxMzI1MCwxMTI5MyAxMzMwMywxMTEzMSAxMzI5MiwxMDkzNyAxMzEyOCwxMTA2MSAxMjcxNSwxMTAwOSBMIDEyNjc1LDEwNzQyIEMgMTI3MTcsMTA2MTYgMTI2ODksMTA2NDYgMTMxMzQsMTA2NTIgMTMzMTMsMTA2NTQgMTM0OTUsMTA2NTIgMTM2NzQsMTA2NTIgTCAxMjQ3NSwxMDU5MSAxMjQ3NSwxMTc5MCAxMzY3MywxMTc5MCAxMzY3NCwxMTY2OSAxMjg4OSwxMTY3NiBDIDEyNTQ1LDExNjQ2IDEyNzYzLDExNjg2IDEyNjc1LDExNTc5IFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMjU1LDI1NSwyNTUpIiBkPSJNIDEyNzQwLDc3ODEgQyAxMjgwMiw4MDUyIDEyNjYwLDc4MDYgMTI5NTYsNzk2MiBMIDEyODUxLDg1OTQgQyAxMzE2Nyw4NTI1IDEzNTYwLDgyODIgMTM2NDgsODA4MSAxMzQ3MCw3NjczIDEyOTA3LDc2MzkgMTI3NDAsNzc4MSBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDI3LDI1LDI0KSIgZD0iTSAxMjQwMiwxMTg1OCBMIDEzNzY0LDExODQ1IDEzNzY3LDExNjI2IDEyNjc1LDExNTc5IEMgMTI3NjQsMTE2ODUgMTI1NDYsMTE2NDYgMTI4ODgsMTE2NzUgTCAxMzY3NCwxMTY2OCAxMzY3MywxMTc5MCAxMjQ3NSwxMTc5MCAxMjQ3NiwxMDU5MCAxMzY3NCwxMDY1MiBDIDEzNDk0LDEwNjUyIDEzMzE0LDEwNjU0IDEzMTM0LDEwNjUxIDEyNjg5LDEwNjQ1IDEyNzE4LDEwNjE1IDEyNjc1LDEwNzQyIEwgMTM3MDEsMTA3MDggMTM3MDEsMTA1MzYgMTI0MDksMTA1MzIgMTI0MDIsMTE4NTggWiIvPgogIDxwYXRoIGZpbGw9InJnYigyNTUsMjU1LDI1NSkiIGQ9Ik0gNjk3Nyw4NzYyIEwgNzYwOCw4Njc1IEMgNzYxOSw4MjYxIDc2NDgsODAyMCA3MzA5LDc4OTggNzIxMyw4MTY4IDcwNzIsODMyMSA2OTc3LDg3NjIgWiIvPgogIDxwYXRoIGZpbGw9InJnYigyNTUsMjU1LDI1NSkiIGQ9Ik0gMTEwNzksODc2MiBMIDExNzA3LDg2ODggQyAxMTcxMiw4MzEwIDExNzUyLDgwNDcgMTE0NjEsNzkwNyAxMTI5Niw4MTAyIDExMTU4LDg0NjQgMTEwNzksODc2MiBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDE0MSw0MSw0MikiIGQ9Ik0gMTA0NTUsNzExMSBMIDEwOTQ2LDcwODYgQyAxMTA4Miw2NTYxIDExMzE4LDY0MTggMTE3MzcsNjE5MiAxMTQyNyw2MTc2IDExMTAyLDYzNjQgMTA5NTIsNjQ5OCAxMDc5Miw2NjQxIDEwNDkwLDY5MjAgMTA0NTUsNzExMSBaIi8+CiAgPHBhdGggZmlsbD0icmdiKDI3LDI1LDI0KSIgZD0iTSAxMjQwMSwxMTg1OCBMIDEyNDA5LDEwNTMyIDEzNzAxLDEwNTM2IDEzNzAxLDEwNzA4IEMgMTM3NjQsMTA2MjIgMTM3ODYsMTA4MjEgMTM3NTcsMTA1MDcgTCAxMjM3MiwxMDQ3NCBDIDEyMzM5LDEwNzIyIDEyMzUxLDEwOTUyIDEyMzUwLDExMTkwIDEyMzUwLDExMzkxIDEyMzExLDExNjgyIDEyNDAxLDExODU4IFoiLz4KICA8cGF0aCBmaWxsPSJyZ2IoMjU1LDI1NSwyNTUpIiBkPSJNIDcxMTYsMTEyODkgTCA3NjY2LDExMjg3IDc0MTYsMTA4MDcgNzExNiwxMTI4OSBaIi8+Cjwvc3ZnPg=="
        alt="Zamar Valley"
      >
    </div>

    <!-- FORM FIELDS -->
    @php
      $c = $booking->customer;
      $p = $booking->plot;
    @endphp
    <div class="field-group">

      <div class="field-row">
        <span class="field-label">Name</span>
        <div class="field-line" style="display:flex;align-items:flex-end;padding-bottom:1px;font-weight:600;">{{ $c->name ?? '' }}</div>
      </div>

      <div class="field-row">
        <span class="field-label">Plot</span>
        <div class="field-line" style="display:flex;align-items:flex-end;padding-bottom:1px;font-weight:600;">#{{ $p->plot_number ?? '' }}</div>
      </div>

      <div class="field-row">
        <span class="field-label">Street</span>
        <div class="field-line" style="display:flex;align-items:flex-end;padding-bottom:1px;font-weight:600;">{{ $p->street_number ?? '' }}</div>
      </div>

      <div class="field-row-inline">
        <span class="field-label">Block</span>
        <div class="field-line-short" style="display:flex;align-items:flex-end;padding-bottom:1px;font-weight:600;">{{ $p->block ?? '' }}</div>
        <span class="field-label" style="margin-left:4mm;">Size</span>
        <div class="field-line-short" style="display:flex;align-items:flex-end;padding-bottom:1px;font-weight:600;">{{ ($p->size ?? '') }} {{ ($p->unit ?? '') }}</div>
      </div>

      <div class="field-row">
        <span class="field-label">Type</span>
        <div class="field-line" style="display:flex;align-items:flex-end;padding-bottom:1px;font-weight:600;">{{ ucwords($p->category->name ?? '') }}</div>
      </div>

      <div class="field-row sig-row">
        <span class="field-label">Signature</span>
        <div class="field-line"></div>
      </div>

    </div>
  </div><!-- /col-left -->

  <!-- ════════════ RIGHT COLUMN ════════════ -->
  <div class="col-right">

    <!-- POSSESSION SLIP BANNER -->
    <div class="possession-banner">POSSESSION SLIP</div>

    <!-- CONTACT CARD -->
    <div class="contact-card">
      <span class="phone" style="display:flex;align-items:flex-end;gap:0;white-space:nowrap;">
        Contact No.
        <span style="flex:1;border-bottom:1px solid rgba(255,255,255,0.7);margin-left:4px;min-width:38mm;display:inline-block;">&nbsp;</span>
      </span>
      <span class="name" style="display:flex;align-items:flex-end;gap:0;white-space:nowrap;">
        Received By
        <span style="flex:1;border-bottom:1px solid rgba(255,255,255,0.7);margin-left:4px;min-width:38mm;display:inline-block;">&nbsp;</span>
      </span>
    </div>

  </div><!-- /col-right -->

</div><!-- /slip -->

<!-- ACTION TOOLBAR -->
<div class="action-toolbar">
    <button class="action-btn btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Print
    </button>
    <a class="action-btn btn-download" href="{{ route('booking.weekly.offer.pdf', $booking->id) }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
        </svg>
        Download PDF
    </a>
</div>

</body>
</html>
