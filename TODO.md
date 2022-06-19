
Add to README.md (after rebase on master):
---

In order for user registrations and form-to-email functionality to work,
these settings are required in `elib.yml`:

    ---
    email_user: <email provider username>
    email_password: <email provider password>
    email_host: <email provider host>
    email_port: <email provider port ("25")>
    email_from: <email from user (e.g. "mike@onlinevibes.net")>
    email_organisation: <(e.g. "Online Vibes")>
    email_recipient: <where to send contact form emails - can be different than email_from>



