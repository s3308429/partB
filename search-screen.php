<?php
  require_once('db.php');

#connect to database
if(!$dbconn = mysql_connect(DB_HOST, DB_USER, DB_PW)) {
echo 'Could not connect to mysql on ' . DB_HOST . '\n';
exit;
}

#connect to schema
if(!mysql_select_db(DB_NAME, $dbconn)) {
echo 'Could not use database ' . DB_NAME . '\n';
echo mysql_error() . '\n';
exit;
}
?>

<html>
<head>
<title>winestore</title>
</head>
<body>
<h2>Winestore Search Engine</h2>
	<form action="query.php" method="GET">
		<table>
			<tr>
				<td>Wine Name:</td>
				<td><input type="text" name="wineName"></td>
			</tr>
			<tr>
				<td>Winery Name:</td>
				<td><input type="text" name="wineryName"></td>
			</tr>
			<tr>
				<td>Region Name:</td>
				<td><label>
                                  <select name="regionName">
                                     <?php 
                                      $getRegion=mysql_query("select * from region");
                                      while($viewRegion=mysql_fetch_array($getRegion)){
                                     ?>
                                     <option id="<?php echo $viewRegion['region_id'];?>"><?php echo $viewRegion['region_name'] ?></option>
                                     <?php } ?>
                                   </select>
                        	</label></td>
			</tr>
			<tr>
				<td>Grape Variety:</td>
				 <td><label>
                                  <select name="grapeVariety">
                                     <option id="0">All</option>
                                     <?php
                                      $getVariety=mysql_query("select * from grape_variety");
                                      while($viewVariety=mysql_fetch_array($getVariety)){
                                     ?>
                                     <option id="<?php echo $viewVariety['variety_id'];?>"><?php echo $viewVariety['variety'] ?></option>
                                     <?php } ?>
                                   </select>
                                </label></td>

			</tr>
			<tr>
				<td>Start Year:</td>
                                 <td><label>
                                  <select name="startYear">

                                     <?php
                                      $getStartyear=mysql_query("select distinct year from wine order by year");
                                      while($viewStartyear=mysql_fetch_array($getStartyear)){
                                     ?>
                                     <option id="<?php echo $viewStartyear['year'];?>"><?php echo $viewStartyear['year'] ?></option>
                                    <?php } ?>
                                   </select>
                                </label></td>
			</tr>
			<tr>
				<td>End Year:</td>
                                 <td><label>
                                  <select name="endYear">
                                    <?php
                                      $getEndyear=mysql_query("select distinct year from wine order by year DESC");
                                      while($viewEndyear=mysql_fetch_array($getEndyear)){
                                     ?>
                                     <option id=""><?php echo $viewEndyear['year'] ?></option>
                                     <?php } ?>
                                   </select>
                                </label></td>
			</tr>
			<tr>
				<td>Minimum Number of Wines in Stock:</td>
				<td><input type="text" name="minStock"></td>
			</tr>
			<tr>
				<td>Minimum Number of Wines Orderd:</td>
				<td><input type="text" name="minOrder"></td>
			</tr>
			<tr>
				<td>Maximum Cost:</td>
				<td><input type="text" name="maxCost"></td>
			</tr>
			<tr>
				<td>Minimum Cost:</td>
				<td><input type="text" name="minCost"></td>
			</tr>
		</table>
		<input type="Submit" value="Search wines">
	</form>
</body>
</html>

