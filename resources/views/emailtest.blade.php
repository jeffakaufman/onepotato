<html>
<head>

</head>
<body>
    <strong>Hey! {{ $friendname }} thinks you'd love to receive fresh, delicious meals delivered to your door from One Potato!</strong><br />
	<strong>{{ $custommessage }}</strong><br/>
	<a href="http://<?PHP echo $_SERVER['SERVER_NAME']; ?>/referral/subscribe/?u={{ $referralid }}&f={{ $friendid }}">Click here</a> to order now!
</body>
</html>