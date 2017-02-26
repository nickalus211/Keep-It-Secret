<!DOCTYPE HTML>
<html>

  <head>

    <meta charset = "utf-8">

    <link rel = "icon" href="images/icon.png"/>
    <title>Keep it Secret</title>

    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel = "stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    <script src = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel = "stylesheet" href = "../AllPage.css"/>

    <script>
      $(document).ready(function(){
        var toReturn;

        <?php
          include "../../scripts/Database.php";
          include "../../scripts/ManipulateText.php";
          //Creates a database object
          $database = new Database();
          $database -> connect();
          //Checks if the page form had been filled and submitted
          if(!empty($_POST["recipientPublic"]) && !empty($_POST["messageToEncode"])){
            //Attempts to retrieve recipient's private key from their public key
            $privateKey = $database -> select("SELECT privateKey FROM userKeys WHERE publicKey = " . $database -> quote($_POST["recipientPublic"]) . ";")[0]["privateKey"];
            //Sets the "toReturn" variable to the encoded message unless the main query fails
            if($privateKey === false){
              echo "toReturn = false;";
            }else{
              //The specified row's "lastUsed" column is then updated with a more recent time
              $updatedTime = $database -> query("UPDATE userKeys SET lastUsed = NOW() WHERE publicKey = " . $database -> quote($_POST["recipientPublic"]));
              //Attempts to encrypt message
              $encrypted = "";
              try{
                $encrypted = encodeMessage($_POST["messageToEncode"], $privateKey);
              }catch(Exception $e){
                $encrypted = $e -> getMessage();
              }
              echo "toReturn = \"" . $encrypted . "\";";
            }
            //Clears entered data so that page reloads don't unecessarily trigger modals
            $_POST = array();
          }
        ?>

        //Checks to see if the PHP sent the encoded message
        if(toReturn !== undefined && toReturn !== null){
          //Checks to see if the PHP query amounted to nothing in the case that the query was attempted
          if(toReturn == false){
            //Sends error message
            document.getElementById("status").innerHTML = "There was an error processing your request";
            document.getElementById("encrypted").innerHTML = toReturn;
            console.log("Could not find public key");
          }else{
            //Displays the encoded message
            document.getElementById("status").innerHTML = "Your message has been encoded successfully";
            document.getElementById("encrypted").innerHTML = toReturn;
            console.log("Successfully found public/private key pair and encoded message");
          }
          $("#myModal").modal("show");
        }
      });
    </script>

  </head>

  <body>
    <div class = "main">

      <div class = "masthead">
        <a href = "../index.html"><img src="images/Masthead.png" class = "fit rounded-img" id = "bannerImg"/></a>
      </div>

      <br/>

      <div class = "navbar fit navbar-light siteNav img-rounded">
        <ul class = "nav navbar-nav fit img-rounded specialBlue">
          <li><a href = "../index.html" class = "linkGlyph">Home</a></li>
          <li><a href = "../Generate_Key/index.php" class = "linkGlyph">Generate a Key</a></li>
          <li class = "active"><a href="index.php" class = "linkGlyph">Encode a Message</a></li>
          <li><a href = "../Decode/index.php" class = "linkGlyph">Decode a Message</a></li>
        </ul>
      </div>

      <script>
        $(document).ready(function(){
          document.getElementsByClassName("siteNav")[0].style.marginTop = document.getElementById("bannerImg").height + "px";
        });
      </script>

      <div class = "container fit">
        <div class = "row vertical-align">
          <div class = "col-sm-12 specialBlue img-rounded">
            <h3>Encode a message</h3>
            <p>Here you can encode a message.</p>
            <p>To encode a message, write your message and the recipient's address or public key below. An encoded version of the message will be returned.</p>
            <p>Once your message is encoded, you will need to send the encoded version yourself to the recipient; however, you won't need to worry about your original message being read.</p>
            <p>The encoding process involves a message going through many different phases to make it unreadable. The first step taken is that the recipient's private key is found out from their public key. The recipient's private key is then used as the random number generator's (RNG's) seed. Then, the message gets <a href = "https://en.wikipedia.org/wiki/Caesar_cipher">Caeser Ciphered</a> with each shift amount being a random number for each letter. Then, each letter of the message gets shuffled with random letters being inserted in between. Finally, the number for the amount of letters in the original message is inserted into the message to be used for decrypting it.</p>
          </div>
        </div>
        <br/>
        <div class = "row vertical-align">
          <div class = "col-sm-12 specialBlue img-rounded">
            <br/>
            <form method = "post" action = "index.php">
              <p>Message to encode:</p>
              <textarea class = "fit" rows = "15" name = "messageToEncode"></textarea>
              <br/><br/>
              <p>Recipient's public key:</p>
              <input class = "fit" type = "text" name = "recipientPublic">
              <br/><br/><br/>
              <input class = "fit" type = "submit">
            </form>
            <br/>
          </div>
        </div>
      </div>

      <div id = "myModal" class = "modal fade" role = "dialog">
        <div class = "modal-dialog">
          <div class = "modal-content img-rounded">

            <div class = "modal-header specialBlue">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Encoded Message</h3>
            </div>
            <div class = "modal-body">
              <h3 id = "status"></h3>
              <p id = "encrypted"></p>
            </div>
            <div class = "modal-footer specialBlue">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

          </div>
        </div>
      </div>

      <br/>

      <div class = "container fit footerBox img-rounded">
        <div class = "row">
          <div class = "col-sm-3">
            <a role = "button" href = "mailto:SaurabhTotey@gmail.com" class = "btn btn-block footBtn">
              <h5>Creator Email</h5>
              <p>SaurabhTotey@gmail.com</p>
            </a>
          </div>
          <div class = "col-sm-3">
            <a role = "button" href = "https://github.com/SaurabhTotey" class = "btn btn-block footBtn">
              <h5>Creator GitHub</h5>
              <p>SaurabhTotey</p>
            </a>
          </div>
          <div class = "col-sm-3">
            <a role = "button" href = "https://saurabhtotey.github.io/" class = "btn btn-block footBtn">
              <h5>Creator Portfolio</h5>
              <p>saurabhtotey.github.io</p>
            </a>
          </div>
          <div class = "col-sm-3">
            <a role = "button" href = "#" class = "btn btn-block footBtn">
              <h5>Donate to Creator</h5>
              <p>Every penny helps!</p>
            </a>
          </div>
        </div>
        <br/>
        <div class = "row col-sm-12">
          <p>Page designed by Saurabh Totey. Images created by Elia Gorokhovsky. Code written by Saurabh Totey. Made in February, 2016. Inspired by our original "<a href = "http://www.codeskulptor.org/#user40_fVbe7V0msMMBR45.py">Code Talker Script</a>" and also inspired by the <a href = "http://www.moserware.com/2009/09/stick-figure-guide-to-advanced.html">"Stick Figure Guide to the Advanced Encryption Standard (AES)" by Moserware"</a>.</p>
        </div>
      </div>

    </div>
  </body>

</html>