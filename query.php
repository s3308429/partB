<html>
<head>
<title>view wine</title>
</head>

<body>

	<?php
	 require_once('db.php');
    #connect to database
    if(!$dbconn = mysql_connect(DB_HOST, DB_USER, DB_PW)) {
    echo 'Could not connect to mysql on ' . DB_HOST . '\n';
    exit;
    }

    #connect to schema
    if(!mysql_select_db(DB_NAME, $dbconn)) {
    echo 'Could not user database ' . DB_NAME . '\n';
    echo mysql_error() . '\n';
    exit;
    }
        $wineName =(isset($_GET['wineName'])? $_GET['wineName']:null);
        $regionName =(isset($_GET['regionName'])?$_GET['regionName']:null);
        $wineryName = (isset($_GET['wineryName'])?$_GET['wineryName']:null);
        $startYear =(isset($_GET['startYear'])?$_GET['startYear']:null);
        $endYear =(isset($_GET['endYear'])?$_GET['endYear']:null);
        $grapeVariety =(isset($_GET['grapeVariety'])?$_GET['grapeVariety']:null);
        $minStock =( isset($_GET['minStock'])?$_GET['minStock']:null);
        $minOrder =(isset($_GET['minOrder'])?$_GET['minOrder']:null);
        $minCost = (isset($_GET['minCost'])?$_GET['minCost']:null);
        $maxCost = (isset($_GET['maxCost'])?$_GET['maxCost']:null);

     # Simple validation
	if ($startYear>$endYear){
		echo "<p>Oops!Wrong Order of Year!</p>";
	}

	if(isset($minCost) && $minCost != NULL | isset($maxCost) && $maxCost != NULL){
		if($minCost>$maxCost){
			echo "<p>Oops!Wrong Order of Cost!</p>";
		}
	}

	// Start a query ...
	$query="SELECT wine_name, year, variety, winery_name, region_name,cost,on_hand,SUM(price) AS revenue, SUM(qty) AS sold_no
	FROM wine, grape_variety, winery, region, wine_variety, inventory, items
	WHERE wine.winery_id = winery.winery_id
	AND wine_variety.variety_id = grape_variety.variety_id
	AND wine_variety.wine_id = wine.wine_id
	AND winery.region_id = region.region_id
	AND inventory.wine_id = wine.wine_id
	AND wine.wine_id = items.wine_id";

	// Add extra query if user has further specification
	if (isset($wineName) && $wineName != NULL)
		$query .= " AND wine_name LIKE \"%$wineName%\"";

	if (isset($wineryName) && $wineryName != NULL)
		$query .= " AND winery_name LIKE \"%$wineryName%\"";

	if (isset($regionName) && $regionName != "All")
		$query .= " AND region_name = \"{$regionName}\"";

	if (isset($grapeVariety) && $grapeVariety != "All")
		$query .= " AND variety = \"{$grapeVariety}\"";

	$isSet = false;
	if (isset($minCost) && $minCost != NULL | isset($maxCost) && $maxCost != NULL){
		$query .= " AND cost BETWEEN \"{$minCost}\" AND \"{$maxCost}\"";
		$isSet = true;
	}

	if (isset($startYear) && $startYear != "ALL" | isset($endYear) && $endYear != "ALL")
		$query .= " AND year BETWEEN \"{$startYear}\" AND \"{$endYear}\"";
	$query .=" GROUP BY wine.wine_id";

	if (isset($minStock) && $minStock != NULL){
		$query .= " HAVING on_hand >= \"{$minStock}\"";
	}

	if (isset($minOrder) && $minOrder != NULL){
		if(isset($minStock) && $minStock != NULL){
			$query .= " AND sold_no >= \"{$minOrder}\"";
		}
		else {$query .= " HAVING sold_no>= \"{$minOrder}\"";
		}
	}

	if ($minCost != NULL || $maxCost != NULL){
		if(isset($minCost) && $minCost != NULL | $maxCost == null){
			if($minStock == null && $minOrder == null){
				if(!$isSet){
					$query .= " HAVING cost >= \"{$minCost}\"";
				}
			}
			else {
				$query .= " AND cost >= \"{$minCost}\"";
			}
		}

		if(isset($maxCost) && $maxCost != NULL | $minCost == null){
			if($minStock == null && $minOrder == null){
				if(!$isSet){
					$query .= " HAVING cost <= \"{$maxCost}\"";
				}
			}
			else {$query .= " AND cost <= \"{$maxCost}\"";
			}
		}
	}


	$result = mysql_query($query);
	$rowsFound = @mysql_num_rows($result);

	// Show message if user's search criteria is not found
	if (!$result){
		echo "<p>No record match your search criteria!</p>";
		echo  '<p><a href="search-screen.php">Back</a></p>';
	}

	// Show the results as a table
	if ($rowsFound > 0){
		//echo $query;
                echo  '<p><a href="search-screen.php">Back</a></p>';
		echo "<h1>Results</h1>\n";
		echo "\n<table>\n<tr>\n" .
				"\n\t<th>Wine_Name &nbsp</th>" .
				"\n\t<th>Grape_Variety &nbsp</th>" .
				"\n\t<th>Year &nbsp</th>" .
				"\n\t<th>Winery_Name </th>" .
				"\n\t<th>Region &nbsp</th>" .
				"\n\t<th>Cost &nbsp&nbsp&nbsp</th>" .
				"\n\t<th>Stock &nbsp&nbsp</th>" .
				"\n\t<th>Stock_Sold &nbsp&nbsp</th>" .
				"\n\t<th>Sales_revenue</th></tr>";

		while ($row = @ mysql_fetch_array($result))
		{
			echo  "\n\t\n\t<td>{$row["wine_name"]} </td>" .
			"\n\t\n\t<td>{$row["variety"]}</td>" .
			"\n\t\n\t<td>{$row["year"]} &nbsp&nbsp&nbsp</td>" .
			"\n\t\n\t<td>{$row["winery_name"]} &nbsp&nbsp&nbsp</td>" .
			"\n\t\n\t<td>{$row["region_name"]} &nbsp&nbsp&nbsp</td>" .
			"\n\t\n\t<td>{$row["cost"]} &nbsp&nbsp</td>" .
			"\n\t\n\t<td>{$row["on_hand"]}</td>" .
			"\n\t\n\t<td>{$row["sold_no"]} &nbsp&nbsp</td>" .
			"\n\t\n\t<td>{$row["revenue"]}</td></tr>";


		}
		echo "\n</table>";
		echo "<hr>";
		// Report how many rows were found
		echo "{$rowsFound} records found matching your criteria<br>";

	}
	else {
		echo "<p>No record match your search criteria!</p>";
		echo  '<p><a href="search-screen.php">Back</a></p>';
	}
	mysql_close($dbconn);
	?>
</body>
</html>

