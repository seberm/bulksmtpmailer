<?php

//
echo base64_encode("seberm@science-agency.cz\0seberm@science-agency.cz\0mandarinka");

/*
 * nejdriv musi byt vychytana SMTP trida.. a pak az se muze pokracovat v BULKU
 * http://postfix.state-of-mind.de/patrick.koetter/smtpauth/smtp_auth_mailclients.html
 * http://localhost/projects/BulkSMTPMailer/src/test/index.php
 * 

[seberm@katie ~]$ telnet localhost 9876
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
220 smtp.skok.cz ESMTP
ehlo smtp.skok.cz
250-smtp.skok.cz Hello smtp.skok.cz [83.167.228.39]
250-SIZE 52428800
250-PIPELINING
250-AUTH PLAIN LOGIN
250-STARTTLS
250 HELP
AUTH LOGIN PLAIN c2ViZXJtLWFnZW5jeS5jegBzZWJlcm0tYWdlbmN5LmN6AG1hbmRhcmlua2E=
501 Invalid base64 data
AUTH PLAIN c2ViZXJtLWFnZW5jeS5jegBzZWJlcm0tYWdlbmN5LmN6AG1hbmRhcmlua2E=
535 Incorrect authentication data
AUTH PLAIN c2ViZXJtQHNjaWVuY2UtYWdlbmN5LmN6AHNlYmVybUBzY2llbmNlLWFnZW5jeS5jegBtYW5kYXJpbmth 
235 Authentication succeeded
HELP
214-Commands supported:
214 AUTH STARTTLS HELO EHLO MAIL RCPT DATA NOOP QUIT RSET HELP


*/


?> 

