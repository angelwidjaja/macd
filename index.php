<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('webappdicoding').";AccountKey=".getenv('zyRWRJXkv/O8qC2Dj4OBFAYL33oB9nRic+tkw6VVoie+AB9Zt+syx5AHZflMKgzfF3DWZIlrxazr5gDKA48yYQ==');
$containerName = "image";

$blobClient = BlobRestProxy::createBlobService($connectionString);

try {
  $createContainerOptions = new CreateContainerOptions();
  $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
  $createContainerOptions->addMetaData("key1", "value1");
  $createContainerOptions->addMetaData("key2", "value2");
  $blobClient->createContainer($containerName, $createContainerOptions);
} catch(ServiceException $e){
      
    }
    catch(InvalidArgumentTypeException $e){
     
    }


if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
    <title>Submission</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  </head>
  <body>
    <h1> Analisa Gambar</h1>
    <div class="mt-4 mb-2">
      <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png">
        <input type="submit" name="submit" value="Upload">
      </form>
    </div>
    <br>
    <table class='table table-hover'>
      <thead>
        <tr>
	  <th>File URL</th>
	  <th>Action</th>
	 </tr>
      </thead>
      <tbody>
<script type="text/javascript">
    function processImage(sourceImageUrl) {

        var subscriptionKey = "4218041b49374ddeab1bd6a7c5fb6096";
 
        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 	
        document.querySelector("#sourceImage").src = sourceImageUrl;
 
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>
        <?php
	  do {
	    foreach ($result->getBlobs() as $blob)
	      {
		?>
		<tr>
		  <td><?php echo $blob->getUrl() ?></td>
		  <td>
		    <button onclick="processImage('<?php echo $blob->getUrl() ?>')">Analisa</button>
		  </td>
		</tr>
		<?php
	      }
	    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
	  } while($result->getContinuationToken());
	?>
      </tbody>
    </table>
    <br><br>
    <div id="wrapper" style="width:1020px; display:table;">
    <div id="jsonOutput" style="width:600px; display:table-cell;">
        Response:
        <br><br>
        <textarea id="responseTextArea" class="UIInput"
                  style="width:580px; height:400px;"></textarea>
    </div>
    <div id="imageDiv" style="width:420px; display:table-cell;">
        Source image:
        <br><br>
        <img id="sourceImage" width="400" />
    </div>
</div>
  </body>
</html>
