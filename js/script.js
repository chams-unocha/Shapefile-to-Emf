$(document).ready(function()
{	

});

function ConvertForEmf(){
	var rsl = data;
	var fileContent = "";
	var name = "";
	var type = "";
	//console.log(rsl);
	
	
		for(i=0; i < rsl.features.length; i++) {
			name = rsl.features[i].properties.admin2Pcod;
			type = rsl.features[i].geometry.type;
			
			if(name=="NG024006"){
				console.log(rsl.features[i]);
				
			}
			
			
			if(type == 'Polygon' || type == "MultiPolygon"){
				var coordinates = rsl.features[i].geometry.coordinates;
				fileContent+=rsl.features[i].properties[$("#fileFields").val()]+"*";
				
				if(type == 'Polygon'){
					for(j=0; j < coordinates.length; j++) {
						var points = {};
						points = coordinates[j];
						//console.log(i+","+coordinates.length+" -- "+points.length);
						for(w=0; w < points.length; w++) {
							//console.log(points[w][0]);
							fileContent+=points[w][0]+",";
							fileContent+=points[w][1]+",";
						}
						fileContent+="_";
					}
				}
				
				if(type == 'MultiPolygon'){
					for(j=0; j < coordinates.length; j++) {
						var polygone = {};
						polygone = coordinates[j];
						for(k=0; k < polygone.length; k++) {
							var points = {};
							points = polygone[k];
							//console.log(i+","+coordinates.length+" -- "+points.length);
							for(w=0; w < points.length; w++) {
								//console.log(points[w][0]);
								fileContent+=points[w][0]+",";
								fileContent+=points[w][1]+",";
							}
							fileContent+="_";
						}
					}
				}
				
				
				fileContent+="#";
			}else{
				//console.log("Not polygone");
				//console.log(rsl.features[i]);
			}
		}
	
	
	
	var blob = new Blob([fileContent], {type: "text/plain;charset=utf-8"});
	saveAs(blob, "ShapeFileToEmf.txt");
	
}

function getXMLHttpRequest() {
	var xhr = null;
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	return xhr;
}
function GetExtension(fileName){
	return fileName.slice((fileName.lastIndexOf(".") - 1 >>> 0) + 2);
}
function upload(file){
	
	//UPLOAD DES SHAPEFILES
	$('#loadBloc').html("<div class='loaderX'></div>");
	$('#uploadBloc').hide();
	if(GetExtension(file.name)=="zip"){
		
		var formdata = new FormData();
		formdata.append("upload", file);
		formdata.append('targetSrs', 'EPSG:4326');
		 $.ajax({
            url: 'https://ogre.adc4gis.com/convert',
            data: formdata,
            type: "POST",
            processData: false,
            contentType: false,
            success: function(msg) {
                //console.log("Success: "+msg);
                data = JSON.parse(JSON.stringify(msg));
				//console.log(data);
				var TmpfileName = file.name;
				var id = TmpfileName.replace(/[^A-Za-z]/g,"");
				var fileName = TmpfileName.replace('.zip',"");
				var header = GetShapeFileHeader(data);
				
				//console.log(header);
				

				var selectMapLabel = document.getElementById('fileFields');
				for(var i = 0; i < header.length; i++) {
					var opt = header[i];
					var el = document.createElement("option");
					el.textContent = opt;
					el.value = opt;
					selectMapLabel.appendChild(el);
				}

				$('#loadBloc').html("");
				$('#uploadBloc').show();
            },
            error: function(jqXHR,msg,errorThrown) {
              console.log(jqXHR.status);
			  var error = undefined;
			  try{
				  error = JSON.parse(msg.responseText);
			  }catch(e){
				  alert('An error occured contact the support');
			  }
			  
				$('#loadBloc').html("");
				switch (jqXHR.status) {
					case 0:
						$('#loadBloc').append("<div class='alert alert-danger' role='alert'>Try to<br/>* Check your internet connection<br/>* Check the integrety of your file <br/>* Convert it at <a href='https://mapshaper.org/' target='_blank'> https://mapshaper.org/</a></div>");
						break;
					default:
						$('#loadBloc').append("<div class='alert alert-danger' role='alert'>"+jqXHR.status+" : "+errorThrown+"<br/><strong>Try again or contact the support</strong></div>");
						break;
				}

				
				
				$('#convert').attr('disabled', 'disabled');
            }
      });
    }
	
}

function GetShapeFileHeader(mapData){
	if(mapData.type="FeatureCollection"){
		//RECUPERATION DES ENTETES DU SHAPEFILE
		
		var features = mapData.features;
		var ligne = features[0].properties;
		//console.log(ligne);
		var arrayPropreties = [];
		for(var key in ligne) {
			arrayPropreties.push(key);
			//console.log('key: ' + key + '\n' + 'value: ' + ligne[key]);
		}
		
		return arrayPropreties;
	}
}
