<!doctype html>
<html lang="en" ng-app="blueRidge">
<head>
	<meta charset="utf-8">
	<title>BlueRidge App</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="/css/app.css"/>
	<link rel="stylesheet" href="/css/bootstrap-responsive.min.css"/>
</head>
<body>
	<div id="wrap">
		<div class="container-narrow">
			<div class="masthead">
				<h3 class="muted"><a href="/">BlueRidge</a></h3>
			</div>
			<hr>
			<div ng-view></div>
		</div>
		<div id="push"></div>
	</div>
	<div id="footer">
		<div class="container">
			<p class="muted credit">&copy; 2013 &mdash; Made in Atlanta, GA by <a href="http://ninelabs.com">Nine Labs</a></p>
		</div>
	</div>
	<script src="/lib/angular/angular.min.js"></script>
	<script src="/lib/angular/angular-resource.min.js"></script>
	<script src="/js/app.js"></script>
	<script src="/js/services.js"></script>
	<script src="/js/controllers.js"></script>
</body>
</html>