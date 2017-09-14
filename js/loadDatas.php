<?php
	header('Content-Type: text/html; charset=iso-8859-15');
	$pays = $_POST['pays'];
	
	//DATABASE PARAMS
	$servername = "mysql1.000webhost.com";
	$username = "a2339134_md";
	$password = "13x0186alaw";
	$dbname = "a2339134_md";
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//GET PAYS NAME
	$stmt = $conn->query("SELECT libellePays,descriptionPays,drapeaux FROM pays where idpays='".$pays."'");
	$PaysInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//GET ORGANISATION FUNDS
	$stmt = $conn->query("SELECT idCountry, Organisation, sum(requestedFund) as montantDemande, (sum(requestedFund) - sum(funded)) as montantRestant FROM project where idcountry='".$pays."' group by Organisation");
	$OrganisationResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$NumberOrganisation = $stmt->rowCount();
	
	//GET TOTAL ORGANISATION FUNDS
	$stmt = $conn->query("SELECT sum(requestedFund) as montantDemande FROM project where idcountry='".$pays."' and Organisation is not null");
	$OrganisationTotalRequest = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//GET CLUSTER FUNDS
	$stmt = $conn->query("SELECT idCountry, cluster, sum(requestedFund) as montantDemande, (sum(requestedFund) - sum(funded)) as montantRestant FROM project where idcountry='".$pays."' group by cluster");
	$ClustersResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$NumberCluster = $stmt->rowCount();
	
	//GET TOTAL ORGANISATION FUNDS
	$stmt = $conn->query("SELECT sum(requestedFund) as montantDemande FROM project where idcountry='".$pays."' and cluster is not null");
	$ClusterTotalRequest = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//GET TOTAL ADMIN TARGET REALISATION
	$stmt = $conn->query("SELECT Admin1,sum(TargetTotal) as objectif, (sum(TargetTotal)-sum(AchievedTotal)) as ObjectifRestant FROM `activities`  where idPays='".$pays."'group by Admin1");
	$AdminResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$NumberAdmin = $stmt->rowCount();
	
	
	
	
		

?>

<!DOCTYPE HTML>
<html>

<head>
	<link rel="stylesheet" href="css.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
</head>

<body>

	<div class='Corps'>
		<img class='fermer' src='images/fermer.png' onclick='CloseDetail()'/>
		<div class='blockPays'>
			
			<img class='Drapeau' src='images/<?php echo $PaysInfo[0]['drapeaux'];?>'/>
			<span class="titrePays"><?php echo $PaysInfo[0]['libellePays'];?></span>
			
		</div>
		<div class="DescriptionPays">
			<?php echo $PaysInfo[0]['descriptionPays'];?>
		</div>
		
		
		<div class="BlocGraphique">
			<?php 
				if($NumberOrganisation!=0)
				{
					?>
					<div class="TitreGraphique" id="TitreGraph1" onclick="ToggleGraph('Graph1')">
						<img class='Drapeau' src='images/iconGraph.png'/>
						 Projets Organisations <span class="TotalAmount">(<?php echo $OrganisationTotalRequest[0]['montantDemande'];?>)</span>
					</div>
					<div class="TitreGraphique">
						<canvas id="Graph1" width="250" height="250"></canvas>
					</div>
					
					<?php 
				}
				
				if($NumberCluster!=0)
				{
					?>
					<div class="TitreGraphique" id="TitreGraph2" onclick="ToggleGraph('Graph2')">
						<img class='Drapeau' src='images/iconGraph.png'/>
						 Projets Clusters <span class="TotalAmount">(<?php echo $ClusterTotalRequest[0]['montantDemande'];?>)</span>
					</div>
					<div class="TitreGraphique">
						<canvas id="Graph2" width="250" height="250"></canvas>
					</div>
					<?php 
				}
				
				if($NumberAdmin!=0)
				{
					?>
					<div class="TitreGraphique" id="TitreGraph3" onclick="ToggleGraph('Graph3')">
						<img class='Drapeau' src='images/iconGraph.png'/>
						 Objetifs des Admin
					</div>
					<div class="TitreGraphique">
						<canvas id="Graph3" width="250" height="250"></canvas>
					</div>
					<?php 
				}
			?>
		</div>
	</div>


  <script>
    var ctx = document.getElementById("Graph1").getContext("2d");
    var data = {
      labels: [
		<?php 
			for ($i = 0; $i < $NumberOrganisation; $i++)
			{
				if($i!=($NumberOrganisation - 1))
				{
					echo "'".$OrganisationResults[$i]['Organisation']."',";
				}
				else
				{
					echo "'".$OrganisationResults[$i]['Organisation']."'";
				}
			}
		?>
	  ],
      datasets: [{
        label: "Montant demandé",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "rgba(220,220,220,1)",
        pointColor: "rgba(220,220,220,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: [
			<?php 
			for ($i = 0; $i < $NumberOrganisation; $i++)
			{
				if($i!=($NumberOrganisation - 1))
				{
					echo $OrganisationResults[$i]['montantDemande'].",";
				}
				else
				{
					echo $OrganisationResults[$i]['montantDemande'];
				}
			}
			?>
		]
      }, {
        label: "Montant restant",
        fillColor: "rgba(151,187,205,0.2)",
        strokeColor: "rgba(151,187,205,1)",
        pointColor: "rgba(151,187,205,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(151,187,205,1)",
        data: [
			<?php 
				for ($i = 0; $i < $NumberOrganisation; $i++)
				{
					if($i!=($NumberOrganisation - 1))
					{
						echo $OrganisationResults[$i]['montantRestant'].",";
					}
					else
					{
						echo $OrganisationResults[$i]['montantRestant'];
					}
				}
			?>
		
		]
      }]
    };
    var MyNewChart = new Chart(ctx).Line(data);
  </script>
  
  <script>
    var ctx = document.getElementById("Graph2").getContext("2d");
    var data = {
      labels: [
		<?php 
			for ($i = 0; $i < $NumberCluster; $i++)
			{
				if($i!=($NumberCluster - 1))
				{
					echo "'".$ClustersResults[$i]['cluster']."',";
				}
				else
				{
					echo "'".$ClustersResults[$i]['cluster']."'";
				}
			}
		?>
	  ],
      datasets: [{
        label: "Montant demandé",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "rgba(220,220,220,1)",
        pointColor: "rgba(220,220,220,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: [
			<?php 
			for ($i = 0; $i < $NumberCluster; $i++)
			{
				if($i!=($NumberCluster - 1))
				{
					echo $ClustersResults[$i]['montantDemande'].",";
				}
				else
				{
					echo $ClustersResults[$i]['montantDemande'];
				}
			}
			?>
		]
      }, {
        label: "Montant restant",
        fillColor: "rgba(151,187,205,0.2)",
        strokeColor: "rgba(151,187,205,1)",
        pointColor: "rgba(151,187,205,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(151,187,205,1)",
        data: [
			<?php 
				for ($i = 0; $i < $NumberCluster; $i++)
				{
					if($i!=($NumberCluster - 1))
					{
						echo $ClustersResults[$i]['montantRestant'].",";
					}
					else
					{
						echo $ClustersResults[$i]['montantRestant'];
					}
				}
			?>
		
		]
      }]
    };
    var MyNewChart = new Chart(ctx).Line(data);
  </script>
  
  <script>
    var ctx = document.getElementById("Graph3").getContext("2d");
    var data = {
      labels: [
		<?php 
			for ($i = 0; $i < $NumberAdmin; $i++)
			{
				if($i!=($NumberAdmin - 1))
				{
					echo "'".$AdminResults[$i]['Admin1']."',";
				}
				else
				{
					echo "'".$AdminResults[$i]['Admin1']."'";
				}
			}
		?>
	  ],
      datasets: [{
        label: "Montant demandé",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "rgba(220,220,220,1)",
        pointColor: "rgba(220,220,220,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: [
			<?php 
			for ($i = 0; $i < $NumberAdmin; $i++)
			{
				if($i!=($NumberAdmin - 1))
				{
					echo $AdminResults[$i]['objectif'].",";
				}
				else
				{
					echo $AdminResults[$i]['objectif'];
				}
			}
			?>
		]
      }, {
        label: "Montant restant",
        fillColor: "rgba(151,187,205,0.2)",
        strokeColor: "rgba(151,187,205,1)",
        pointColor: "rgba(151,187,205,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(151,187,205,1)",
        data: [
			<?php 
				for ($i = 0; $i < $NumberAdmin; $i++)
				{
					if($i!=($NumberAdmin - 1))
					{
						echo $AdminResults[$i]['ObjectifRestant'].",";
					}
					else
					{
						echo $AdminResults[$i]['ObjectifRestant'];
					}
				}
			?>
		
		]
      }]
    };
	
    var MyNewChart = new Chart(ctx).Line(data);
  </script>
</body>
</html>