<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Cuneiform Annotator</title>
    <style type="text/css" media="screen">
        body { background:#eee;  text-align:center; }
        canvas { display:block;  background:#fff; border:1px solid #ccc; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);
                  -moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);
                  -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);} 
        input[type="button"]{background: #fff; border: 1px solid #000;text-shadow: 1px 1px 1px #000;cursor: pointer; width:45px; height:25px; font-family:Calibri; font-size: 0.8em;}
        input[type="submit"]{background: #fff; border: 1px solid #000;text-shadow: 1px 1px 1px #000;cursor: pointer; width:45px; height:25px; font-family:Calibri; font-size: 0.8em;}
        h1{ color: #F8F8FF; text-shadow: 1px 1px 1px #000;  font-family:Calibri; } 
        h3{ color: #708090; font-size: 1.10em; margin:0px; border:0px; font-family:Calibri;}
        p{color: grey; margin:5px; border:0px; font-family:Calibri;}
        .table { display: table;      }
        .table_row{ display: table-row; width:100%;}
        .table_cell_lb { display: table-cell; text-align:right; padding:5px; height: 20px; line-height: 20px;}
        /*.table_cell_inp { display: table-cell; text-align: center; border: 1px solid #000;}*/

        a:link    {color:black; background-color:transparent; text-decoration:none}
        a:visited {color:black; background-color:transparent; text-decoration:none}
        a:hover   {color:#708090; background-color:transparent; text-decoration:underline}
        a:active  {color:black; background-color:transparent; text-decoration:underline}

 /**:focus {
    outline: 0;
}*/
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <LINK REL="SHORTCUT ICON" HREF="logo.ico">


    <div id='form1' style="position:relative; top:110px; left:10px; z-index:1;">

        <div id='d1' style="position:absolute; top:0px; left:300px; z-index:1">  
            <canvas id="canvas" width="600" height="800" style="border:1px solid #d3d3d3; cursor: move;">
                Your browser does not support the HTML5 canvas tag. </canvas>    <!--The content inside the <canvas> ... </canvas> tags can be used as a fallback for browsers which don't support canvas rendering.-->
            <div id="wait" style="background:transparent; z-index:5;display:none;width:200px;height:200px;position:absolute;top:30%;left:30%;padding:2px;"><img src='loading.gif' width="200" height="200" /><br></div>
            
            <div id="button_clear" style="position:absolute; top:5px; left:10px; z-index:3;">
                  <input type="button" id="clear" value="Clear All" style="width:60px;">
            </div>

            <div id="button_clear_last" style="position:absolute; top:5px; left:75px; z-index:3;">
                  <input type="button" id="clear_last" value="Clear Last" style="width:65px;">
            </div>

            <div id="button_next" style="position:absolute; top:5px; left:145px; z-index:4">
                  <input type="button" id="next" value="Next">
            </div>

            <div id="button_pan" style="position:absolute; top:5px; left:500px; z-index:4">
                  <input type="button" id="pan" value="Pan" >
            </div>

            <div id="button_draw" style="position:absolute; top:5px; left:550px; z-index:4">
                  <input type="button" id="draw" value="Draw">
            </div>


        </div>

        <div id='d2' style="position:absolute; top:0px; left:920px;  width:300px; height:300px; z-index:2;   border:1px solid #d3d3d3;">
            <canvas id="canvas_small" width="300" height="300" style="border:1px solid #d3d3d3;">
              Your browser does not support the HTML5 canvas tag.</canvas>

            <div id="button_save" style="position:absolute; top:275px; left:255px; z-index:9">
              <input type="button" id="save" value="Save" >
        </div>

        </div>

        



     


       <div id="form_div" style="position:absolute; top:0px; left:0px; z-index:5; height:160px; align:right; width: 260px; background:#fff; border:1px solid #ccc; padding: 10px; margin:0px;" >
              <h3>Login</h3>
              <form class="table" id="login_form" action="login.php" method="POST"  style="align:left;">
                <p class="table_row">
                  <label class="table_cell_lb">First Name: </label>
                  <input  class="table_cell_inp" type="text" name="firstname" value="" style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb">Last Name: </label>
                  <input class="table_cell_inp" type="text" name="lastname" value="" style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb">E-mail: </label>
                  <input class="table_cell_inp" name="mail" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" value=""   style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb">Institute: </label>
                  <input class="table_cell_inp" type="text" name="institute" value=""   style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb"></label>
                  <input class="table_cell_inp" type="submit" value="Save" />
                </p>
             </form>
        </div>

        <div id='info' style=" display:none; position:absolute; top:20px; left:0px; z-index:5; width: 260px; height:150px; background:#fff; border:1px solid #ccc; padding: 10px; margin:0px;">
          <h3>Tablet Info</h3>
          <p id="id" align="left"><strong>ID: </strong><p>
          <p id="name" align="left"><strong>Name: </strong><p>
          <p id="collection" align="left"><strong>Collection:</strong></p>
          <p id="era" align="left"><strong>Era:</strong></p>

        </div>

        <div id='instructions' style="position:absolute; top:190px; left:0px; z-index:5; width: 260px; background:#fff; border:1px solid #ccc; padding: 10px; margin:0px;"> 
            <h3>Instructions</h3>
            <p align="left" style="font-size:0.97em; font-family:Calibri">
              <b>1.</b> Login to load first image. E-mail, first and last name are required.<br>
              <b>2.</b> Zoom using mouse wheel, move using "pan" button.<br>
              <b>3.</b> For annotating a sign choose "Draw" and create a polygon around it by inserting points. Close by clicking close to initial point. The selected area will appear in the small box on the top right.<br>
              <b>4.</b> Use "Clear All" to clear all polygons, "Clear Last" to undo last polygon.<br>
              <b>5.</b> Enter the <a href="https://archive.org/details/MesopotamischesZeichenlexikon" target="_blank">Borger 2003</a> 3-digit index number for the symbol. You can download the alphabetical sign list <a href="Alphabetical list Borger 2003.pdf" download="Alphabetical_list_Borger_2003">here</a>. See the transliteration in the <a href="http://cdli.ucla.edu/" target="_blank">CDLI library</a> for help in identifying symbols. <br>
              <b>6.</b> Adding a comment is optional.<br>
              <b>7.</b> The number of wedges needs to be specified.<br>
              <b>8.</b> Use the radio buttons to rate the quality of the sign.<br>
              <b>9.</b> Use "Save" button to save cropped image and annotations to the KU Leuven server. <b>Warning:</b> This step cannot be undone; make sure the annotation is correct before hitting "Save".<br>
              <b>10.</b> When you are finished with the signs on the current image, press "Next" to load another image.</p>
        </div>

       <!--  <div id='d3' style="position:absolute; top:800px; left:620px; z-index:5"> 
            <textarea  style="height:50px;width:250px" id="txt" rows="1" cols="20"></textarea>
        </div> -->
        <div id="annotation" style="position:absolute; top:320px; left:920px; z-index:5; width: 280px; background:#fff; border:1px solid #ccc; padding: 10px; margin:0px;" >
          <h3>Annotation</h3>
          <form class="table" id="annotation_form" >
                <p class="table_row">
                  <label class="table_cell_lb">Sign: </label>
                  <input  class="table_cell_inp" type="number" oninput="maxLengthCheck(this)" maxlength = "3" min = "1" max = "999" id="num_anno" value="" style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb"># of Wedges: </label>
                  <input class="table_cell_inp" type="number" oninput="maxLengthCheck(this)" maxlength = "3" min="1" max="999" id="num_wedges" value=""   style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb" style="vertical-align=middle;"> Comments:</label>
                  <textarea class="table_cell_inp" id="comment" value="" rows="2" style=" width: 150px; background: #fff; border: 1px solid #000; padding:1px; margin:3px; "></textarea>
                  <!-- <input class="table_cell_inp" type="text" id="comment" value="" style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; "> -->
                </p>
                
               <!--  <p class="table_row">
                  <label class="table_cell_lb">Institute: </label>
                  <input class="table_cell_inp" type="text" name="institute" value=""   style=" width: 150px; height: 15px; background: #fff; border: 1px solid #000; ">
                </p>
                <p class="table_row">
                  <label class="table_cell_lb"></label>
                  <input class="table_cell_inp" type="submit" value="Save" />
                </p> -->
             </form>


        <!--   <label>Sign: </label>
          <textarea  style="height:15px;width:100px" id="num_anno" rows="1" cols="20" ></textarea><br>
          <label align="left" >Comments: </label>
          <textarea  style="height:30px;width:150px" id="comment" rows="1" cols="20" ></textarea> -->
<!--           <select id="signs" style="width: 100px; height: 20px; background: #fff;border: 1px solid #000; text-shadow: 1px 1px 1px #000;"></select>
 -->          
          <br>
          <p>Rate the quality of the sign, based on your experience, from <i>Poor</i> to <i>Excellent</i></p>
          <form action="">
            <label>Poor </label><input type="radio" name="quality" id="rd1" value="1">  <input type="radio" name="quality" id="rd2" value="2">  <input type="radio" name="quality" id="rd3" value="3"> 
            <input type="radio" name="quality" id="rd4"  value="4">  <input type="radio" name="quality" id="rd5" value="5"><label> Excellent</label>
          </form>
        </div>

    </div>
     
</head>
<body >


<div id='banner' style="position:fixed; background:#696969; top:0px; left:0px; width: 3020px;  height:100px; border: 1px solid #000; z-index:20;"> 
  <a href="http://www.esat.kuleuven.be/psi/visics" target="_blank">
       <img src="visics_logo copy.png" alt="VISICS logo" style="position:absolute; top:5px; left:5px; height:90px;">
  </a>
  <div id='header' style="position:absolute; top:-20px; left:180px;">
      <h1>Cuneiform Annotator</h1>
  </div>
  <div id='about' align="left" style="position:absolute; top:30px; left:180px;">
      <p style="color: #E0E0E0 ;">This annotator has been developed by the <a href="http://www.esat.kuleuven.be/psi/visics" target="_blank">VISICS </a> group of KU Leuven. <br> The purpose of this tool is to create training data for machine learning applications on the cuneiform signs. <br> To report any problems please sent an email to gstavrop@esat.kuleuven.be. </p>
  </div>

</div>


<?php
$files = glob('images/*.png') ?>


<script type="text/javascript" > var imageSourceArray= <?php echo json_encode($files); ?>;
//currentImage= <?php echo json_encode($name);?>;
                // var currentImage_id=<?php echo json_encode($id);?>;
                // var currentImage_era=<?php echo json_encode($era);?>;
                // var currentImage_collection=<?php echo json_encode($collection);?>;
                // document.getElementById("name").innerHTML = "<strong>Name: </strong>"+currentImage ;
                // document.getElementById("id").innerHTML = "<strong>ID: </strong>"+currentImage_id ;
                // document.getElementById("era").innerHTML = "<strong>Era: </strong>"+currentImage_era ;
                // document.getElementById("collection").innerHTML = "<strong>Collection: </strong>"+currentImage_collection ;
</script>

<script>
  // This is an old version, for a more recent version look at
  // https://jsfiddle.net/DRSDavidSoft/zb4ft1qq/2/
  function maxLengthCheck(object)
  {
    if (object.value.length > object.maxLength)
      object.value = object.value.slice(0, object.maxLength)
  }
</script>



<script type="text/javascript" src="anno.js"></script>



    
</body>
</html>
