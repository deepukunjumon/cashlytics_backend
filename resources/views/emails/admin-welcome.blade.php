<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:32px 16px;">
<tr><td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background-color:#ffffff;border-radius:12px;overflow:hidden;">
  <tr><td style="background-color:#1e293b;padding:24px 32px;">
    <h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:700;">{{ $appName }}</h1>
  </td></tr>
  <tr><td style="padding:32px;">
    <h2 style="margin:0 0 16px;color:#1e293b;font-size:22px;font-weight:700;">Hey {{ $userName }}</h2>
    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.6;">
      Your <b>{{ $appName }}</b> environment has been successfully set up and your Super Admin account is now active.
    </p>
    <p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.6;">
      Here are your login credentials:
    </p>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
      <tr><td style="padding:8px 0;color:#334155;font-size:14px;">URL: {{ $url }}</td></tr>
      <tr><td style="padding:8px 0;color:#334155;font-size:14px;">Email: {{ $email }}</td></tr>
      <tr><td style="padding:8px 0;color:#334155;font-size:14px;">Temporary Password: {{ $password }}</td></tr>
    </table>
    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.6;">
      For security, please log in and change your password immediately after your first login.
    </p>
    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.6;">
      As Super Admin, you have full access to manage users, configure system settings, and oversee all activity across the platform.
    </p>
    <p style="margin:0;color:#475569;font-size:15px;line-height:1.6;">
      If you run into any issues logging in or have questions about getting started, feel free to reach out.
      <br><br>
      Welcome aboard, and here's to a smooth launch!
    </p>
  </td></tr>
  <tr><td style="padding:20px 32px;background-color:#f8fafc;border-top:1px solid #e2e8f0;">
    <p style="margin:0;color:#94a3b8;font-size:12px;text-align:center;">
      &copy; {{ $year }} {{ $appName }}. All rights reserved.
    </p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>