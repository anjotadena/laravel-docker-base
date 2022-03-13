<table style="width:100%;max-width:960px;margin:0 auto;border-collapse:collapse;border-spacing:0;font-size:14px;line-height:24px;color:#333;font-family:Microsoft YaHei">
    <tbody>
        <tr>
            <td style="padding:20px 7.5% 0;display:block">
                <img src="https://www.kadencewp.com/wp-content/uploads/2020/10/alogo-4.png" class="CToWUd">
            </td>
        </tr>
        <tr>
            <td style="padding:20px 7.5% 0">Dear {{ $name }},</td>
        </tr>

        <tr>
            <td style="padding:20px 7.5% 0">To <span class="il">verify</span> that this email address belongs to you, enter the <span class="il">verification</span> <span class="il">code</span> below into the email <span class="il">verification</span> <span class="il">code</span> box:</td>
        </tr>

        <tr>
            <td style="padding:20px 7.5% 0"> <b style="margin:0;text-decoration:none">{{ $code }}</b> <br>Please do not share your <span class="il">verification</span> <span class="il">code</span> with anyone.</td>
        </tr>

        <tr>
            <td style="padding:20px 7.5% 117px">Sincerely,<br>{{ config('app.name') }}</td>
        </tr>
    </tbody>
</table>