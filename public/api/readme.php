<?php
die('Restricted access');
?>
TopBetta Mobile WebService
==========================

- This webservice has been designed around a loose REST API approach
- You can interact with the API via POST or GET
- All data returned is formatted as JSON data
- There is a status code returned with every response
- There is a lot of code duplication between the API and the Joomla components, but has been done this way to reduce
	any chance of introducing problems into the existing "working" code base and remain maintable seperately
- The basic approach has been to use the existing data models where appropriate and utilise any controller code
	that fit in with the task at hand
