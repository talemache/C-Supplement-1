<script type='text/javascript'>
        
	window.onload = function() {
  		var allelements=document.querySelectorAll('[id="messtxt"]')
		for(i=0;i<allelements.length;i++){
		allelements[i].style.display = 'none';
		}
	};
	
        
	function displaybox(id){
		var allelements=document.querySelectorAll('[id="messtxt"]')
		allelements[id].style.display = 'block';
		
	};
</script>


<?php
$counter=0;
/* Start of things to be done before page is loaded */ 
	require_once('../inc/header.inc.php'); 
    require_once('../inc/code.inc.php');
	require_once("dbconnect.php");
	
	/* Page must have an ID and be valid to execute page*/
	if (isset($_GET['ID'])) {
		/* Main topic information pull */
		try {
			if(!($sql_article = $conn->prepare("SELECT * FROM Article WHERE TopicID = ?;"))) {
				echo '<p class="kentYellow errorBelowNav">Database prepare failure - General</p>';
				mysqli_close($conn);
				exit();
			}
			
			$sql_article->bind_param("i", $_GET['ID']);
			$sql_article->execute();
			$res_article = $sql_article->get_result();
			
			if(!($row_article = $res_article->fetch_assoc())) {
				echo '<p class="kentYellow errorBelowNav">Database fetch failure. ID may be invalid.</p>';
				mysqli_close($conn);
				exit();
			}
			
			$articleName = $row_article['TopicName'];
			$videoLink   = $row_article['VideoLink'];
			$mainCode    = $row_article['Code'];
		}
		catch(Exception $e) {
			echo '<p class="kentYellow errorBelowNav">Database failure for article</p>';
		}
	}
	else {
		echo '<p class="kentYellow errorBelowNav">No Article ID in URL</p>';
		mysqli_close($conn);
		exit();
	}

	if(isset($_POST['comment'])) {
		if($_SESSION["loggedin"]==true){
			try {
				$_comment=$_POST['comment'];
				$_date=date('Y-m-d H:i:s');
				$pNum = $_POST['parentNum'] == '' ? null : $_POST['parentNum'];
				$sql=$conn->prepare("INSERT INTO Comments". "(Text,ID,Time,TopicID,ParentEntryNum)"."VALUES".
				"(?,?,?,?,?)");
				$sql->bind_param("sisii",$_comment,$_SESSION['userid'],$_date,$_GET['ID'],$pNum);
				$result=$sql->execute();
				if(false==$result){
					printf("error:%s\n",mysqli_error($conn));
				}
				if(!$result){
					die('You cannot post your comment. Please try again later.');
				}	
			}
			catch(Exception $e) {
				echo '<p class="kentYellow articleFontSize">Database failure for replies</p>';
			}	
		}else
			echo '<p class="red">You must sign in to add a comment.</p>';
	}
if(isset($_POST['reply'])){
		if($_SESSION["loggedin"]==true){
			try {
				$_comment=$_POST['reply'];
				$_GET['ID'];
				$_date=date('Y-m-d H:i:s');
				$NUMDELETE = 460048219;
				$pNum = $_POST['position'];
				$sql=$conn->prepare("INSERT INTO Comments". "(Text,ID,Time,TopicID,ParentEntryNum)"."VALUES".
				"(?,?,?,?,?)");
				$sql->bind_param("sisii",$_comment,$_SESSION['userid'],$_date,$_GET['ID'],$pNum);
				$result=$sql->execute();
				if(false==$result){
					printf("error:%s\n",mysqli_error($conn));
				}
				if(!$result){
					die('You cannot post your comment. Please try again later.');
				}	
				//mysqli_close($conn);
			}
			catch(Exception $e) {
				echo '<p class="kentYellow articleFontSize">Database failure for replies</p>';
			}	
		}else
			echo '<p class="red">You must sign in to add a comment.</p>';
	}
	
/* Start of functions to load page */
	function displayImages($imgTable, $subnum = NULL) 
	{
		/* Image pull */
		try {
			global $conn;
			
			/* Main area image setup */
			if ($imgTable == "Images") {			
				$stmt = "SELECT * FROM " . $imgTable . " WHERE TopicID = ?;";
				if(!($sql_img = $conn->prepare($stmt))) {
					echo '<p class="kentYellow articleFontSize">Database prepare failure - Images</p>';
					return;
				}
				$sql_img->bind_param("i", $_GET['ID']);
			}
			/* Subtopic area image setup */
			else if ($imgTable == "Subimages") {				
				$stmt = "SELECT * FROM " . $imgTable . " WHERE TopicID = ? AND SubNum = ?;";
				if(!($sql_img = $conn->prepare($stmt))) {
					echo '<p class="kentYellow articleFontSize">Database prepare failure</p>';
					return;
				}				
				$sql_img->bind_param("ii", $_GET['ID'], $subnum);
			}
			else {
				echo '<p class="kentYellow articleFontSize">Image retrieval failure</p>';
				return;
			}
			
			$sql_img->execute();
			$res_img = $sql_img->get_result();
			
			while($row_img = $res_img->fetch_assoc()) {
				echo	'<figure class="text-center">
							<img src="../img/' . $row_img['Filename'] . '" class="figOpts" 
							 alt="';
			                      if ($row_img['Alt'] != NULL) 
							          echo $row_img['Alt'];
							      else 
									  echo 'Alt Text Missing';
				echo         '">
							<figcaption class="justify-center kentYellow">';
				if ($row_img['Figcaption'] != NULL)
					echo	$row_img['Figcaption'];
				else
					echo	"";
				echo 	'</figcaption>
						 </figure>';
			}
				
		}
		catch(Exception $e) {
			echo '<p class="kentYellow">Database failure for ' . $imgTable. '</p>';
		}
	}

	function displaySubtopics() 
	{
		try {
			global $conn;
			global $topicText;
			if(!($sql_sub = $conn->prepare("SELECT * FROM Subtopics WHERE TopicID = ? ORDER BY SubNum ASC;"))) {
				echo '<p class="kentYellow articleFontSize">Database prepare failure - Subtopics</p>';
				return;
			}
			
			$sql_sub->bind_param("i", $_GET['ID']);
			$sql_sub->execute();
			$res_sub = $sql_sub->get_result();
			
			while($row_sub = $res_sub->fetch_assoc()) {
				echo 	'<div class="container-fluid mt-5">
						    <h3 class="intro">' . $row_sub['SubName'] . '</h3>
							<hr>
						 </div>';
				echo    '<div class="container-fluid">
							<div class="row ml-5 mr-5">
								<div class="col-12 section">';
								
				echo    '<p class="break">' . nl2br($row_sub['Text']) . '</p>';
								
				displayImages("Subimages", $row_sub['SubNum']);
				
				displayExCode($row_sub['SubNum']);
				
				/* Ending 3 divs from second echo of while loop */
				echo	'</div>
				         </div>
						 </div>';
			}
		}
		catch(Exception $e) {
			echo '<p class="kentYellow articleFontSize">Database failure for subtopic(s)</p>';
		}
	}
	
	function displayExCode($SubNum) 
	{
		try {
			global $conn;
			global $code;
			global $raw;
			if(!($sql_code = $conn->prepare("SELECT * FROM Subcode WHERE SubNum = ? AND TopicID = ? ORDER BY SubCodeNum ASC;"))) {
				echo '<p class="kentYellow articleFontSize">Database prepare failure - ExCode</p>';
				return;
			}

			$sql_code->bind_param("ii", $SubNum, $_GET['ID']);
			$sql_code->execute();
			$res_code = $sql_code->get_result();
			
			if ($res_code->num_rows > 0)
				echo	'<p class="kentBlue text-center articleFontSize">Example Code:</p>';
			while($row_code = $res_code->fetch_assoc()) {	
				$copy_paste_code = htmlspecialchars($raw[$row_code['SubCodeNum']]);
				echo	'<div class="row mb-4">
							<div class="col-12 col-lg-9 Code_Ex ml-1 mb-1">'
								. $code[$row_code['SubCodeNum']] .
						   '</div>
							<div class="col-11 mt-1 mb-2">
							    <button class="btn btn-success" id="' . $row_code['SubCodeNum'] . '" ';
				                    printf('onclick="copyStringToClipboard(\'%s\',\'%s\')" ', $copy_paste_code , $row_code[SubCodeNum]);
				echo	'		    type="button">Copy Code</button>
								<span>*iOS users, manually copy</span>
							</div>
						 </div>';
			}	
		}
		catch(Exception $e) {
			echo '<p class="kentYellow articleFontSize">Database failure for example code</p>';
		}		
		
	}
	
	function displayQuiz() 
	{
		/* Quiz information pull */
		try {
			global $conn;
			global $code;
			if(!($sql_quiz = $conn->prepare("SELECT * FROM Questions WHERE TopicID = ?;"))) {
				echo '<p class="kentYellow articleFontSize">Database prepare failure</p>';
				return;
			}
			
			$sql_quiz->bind_param("i", $_GET['ID']);
			$sql_quiz->execute();
			$res_quiz = $sql_quiz->get_result();
			
			/* choices is an aid for the output of question options*/
			$choices = array('A', 'B', 'C', 'D', 'E');
			for ($i = 0; ($row_quiz = $res_quiz->fetch_assoc()); ++$i) {
				/* Question */
				echo 	'<label class="mt-4 mb-4 qSize">' 
							. ($i + 1) . ') ' . $row_quiz['Question'] .
						'</label>
						 <br>';
				/* Code (if any) */
				if ($row_quiz['CodeID'] != NULL) {
					echo 	'<div class="col-12 col-lg-9 Code_Ex ml-1 mb-1">'
								. $code[$row_quiz['CodeID']] .
							'</div>
							 <br>';
				}
				/* Options / Choices */
				for($j = 0; $j < count($choices); ++$j) {
					if($row_quiz[$choices[$j]] == NULL)
						continue;
					echo	'<label class="qBox kentYellow">' 
								. $row_quiz[$choices[$j]] .
							'<input type="radio" name="'
								. $row_quiz['QNum'] . 
							'" value = "'
								. $choices[$j] .
							'">
							 <span class="checkmark"></span>
							 </label>';
				}
			}
						
			if ($i === 0) {
				echo '<p class="kentYellow articleFontSize">Database fetch failure or no present quiz questions</p>';
				return;
			}
		}
		catch(Exception $e) {
			echo '<p class="kentYellow articleFontSize">Database failure for quiz</p>';
		}
	}

	function displayComments() {
		global $conn;
		try {
			if(!($sql_OC = 
			        $conn->prepare("SELECT * FROM Comments 
								    WHERE TopicID = ? AND ISNULL(ParentEntryNum)
									ORDER BY EntryNum;"))) {
				echo '<p class="kentYellow articleFontSize">Database prepare failure</p>';
				return;
			}
			
			$sql_OC->bind_param("i", $_GET['ID']);
			$sql_OC->execute();
			$res_OC = $sql_OC->get_result();
				
			while($row_OC = $res_OC->fetch_assoc()) {
				$sql_oUsr="SELECT username FROM Login WHERE ID = $row_OC[ID]";
				$res_oUsr=mysqli_query($conn,$sql_oUsr);
				if (!$res_oUsr) {
					printf("Error: %s\n", mysqli_error($conn));
					exit();
				}
				$oUsr = mysqli_fetch_array($res_oUsr);
				echo 	"<div class='col-12 section mt-5 noWrap'>
							<p class='kentYellow'>$oUsr[username]</p>
							<p>$row_OC[Text]</p>
							<form method='POST' action=''>
								<input type='hidden' name='parentNum' value='NULL'>
								<button onclick= 'displaybox(".$GLOBALS['counter'].")' type='button' class='btn btnKent fa fa-reply'> Reply</button>
								<span style='color: Thistle'> $row_OC[Time] | Post #$row_OC[EntryNum]</span>
							</form>
				         </div>";
					
					echo    '<div style="text-indent:55px" id="messtxt" class="display:none">';
				echo    '<p>Input your reply</p>';
				echo    '<form method=post action="">';
				echo    '<textarea placeholder="Your reply" name="reply" style="padding-left:5px;height:100px; width:500px"  type="text"  id="reply"></textarea><br>';
				
				echo    '<input type="hidden" name="position" value='.$row_OC["EntryNum"].'> ';
				echo	'<button style="float: right" class="btn btnKent fa " type=submit name=submit id=reply>Post</button></form></div>';

					$GLOBALS['counter']++;
					replyCheck($row_OC);
			}
		}
		catch(Exception $e) {
			echo '<p class="kentYellow articleFontSize">Database failure for comments</p>';
		}
	}
	
	function replyCheck($record)
	{	
		global $conn;
		try {
			$sql_repl = "SELECT * from Comments
						 WHERE TopicID = $record[TopicID] AND ParentEntryNum = $record[EntryNum]
						 ORDER BY ParentEntryNum, EntryNum;";
				 
			$res_repl = mysqli_query($conn,$sql_repl);
			
			if(!$res_repl){
				printf("Error: %s\n", mysqli_error($conn));
				exit();
			}
			
			if (mysqli_num_rows($res_repl) > 0)
				displayReplies($res_repl);	
		}
		catch(Exception $e) {
			echo '<p class="kentYellow articleFontSize">Database failure for potential replies</p>';
		}		
	}
	
	function displayReplies($children) 
	{
		$id=0;
		global $conn;
		try {
			while($row_repl = mysqli_fetch_array($children)){
				
				$sql_usr = "SELECT username FROM Login WHERE ID = $row_repl[ID]";
				$sq1_pUsr = "SELECT username 
							 FROM Comments JOIN Login 
							 WHERE EntryNum = $row_repl[ParentEntryNum] 
							   AND Login.ID = Comments.ID;";
				$res_usr = mysqli_query($conn,$sql_usr);
				$res_pUsr = mysqli_query($conn,$sq1_pUsr);
				if (!$res_usr || !$res_pUsr) {
					printf("Error: %s\n", mysqli_error($conn));
					exit();
				}
				$user = mysqli_fetch_array($res_usr);
				$pUser = mysqli_fetch_array($res_pUsr);
				echo '<div class="col-11 mt-3 section ml-auto noWrap">';
				echo 	"<p class='kentBlue'>$user[username]" 
					   ."<span class='kentYellow'> | @$pUser[username] "
					   ."post #$row_repl[ParentEntryNum]</span>" 
					   ."</p>";
				echo 	"<p>$row_repl[Text]</p>";
				echo 	'<button onclick= "displaybox('.$GLOBALS["counter"].')" class="btn btnKent fa fa-reply"> Reply</button>';
				echo 	"<span style='color: Thistle'> $row_repl[Time] | Post #$row_repl[EntryNum]</span>";	
				echo    '</div>';
				echo    '<div style="text-indent:55px" id="messtxt" class="display:none">';
				echo    '<p>Input your reply</p>';
				echo    '<form method=post action="">';
				echo    '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<textarea placeholder="Your reply" name="reply" style="padding-left:5px;height:100px; width:500px"  type="text"  id="reply"></textarea><br>';
				echo    '<input type="hidden" name="position" value='.$row_repl["EntryNum"].'> ';
				echo	'<button style="float: right" type="submit" class="btn btnKent fa fa-reply" name="submit" id="reply">Post</button></form></div>';
				$GLOBALS['counter']++;
				replyCheck($row_repl);
				
			}	
		}
		catch(Exception $e) {
			echo '<p class="kentYellow articleFontSize">Database failure for replies</p>';
		}			
	}
?>
	<div class="content">
			<!-- Main Section - Maybe just a summary? -->
			<div class="container-fluid mt-3">
				<h1 class="intro"><?php echo $articleName ?></h1>
				<hr>
			</div>
			<div class="container-fluid">
				<div class="row ml-5 mr-5">
					<div class="col-12 section">
						<?php 
							echo $row_article['Text'];
							displayImages("Images"); 
						?>				
					</div>
				</div>
			</div>
			
			<!-- Subtopic Section -->
			<?php
				displaySubtopics();
			?>
			
			<!-- Video Section -->
			<div class="container-fluid mt-5">
				<h3 class="intro kentYellow">Video</h3>
				<hr>
			</div>
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-9">
						<?php   if($videoLink != NULL) {
									echo '<div class="embed-responsive embed-responsive-16by9">
										      <iframe class="embed-responsive-item" src="'. $videoLink . '" allowfullscreen>
									          </iframe>
										  </div>';
						        } else
									echo '<p class="kentYellow text-center" id="noVid">No video for topic available</p>';
						?>					
					</div>
				</div>
			</div>
			
			<!-- Quiz Section -->
			<div class="container-fluid mt-5">
				<h3 class="intro kentBlue">Quiz</h3>
				<hr>
			</div>
			<div class="container ml-5">
				<div class="row">
					<div class="col-12">
						<form name="FORM" action="../quizProcess.???" method="post"> 
							<?php displayQuiz(); ?>
						</form>
					</div>
				</div>
			</div>
			
			<!-- Comments Section -->
			<div class="container-fluid mt-5">
				<h3 class="text-white intro">Comments</h3>
				<hr>
			</div>
			<div class="container-fluid">
				<div class="row mb-5 ml-5">
					<div class="col-md-8 col-12">
						<form action="" method="POST">
							<div class="input-group">
								<textarea type="text" rows="5" name="comment" value="comment" placeholder="Enter Comment..." class="form-control"></textarea>
							</div>
							<input type="hidden" name="parentNum" value="">
							<button type="submit" class="btn btnKent mt-2">Submit</button>
						</form>
					</div>
				</div>
				<div class="row mb-5">
					<?php displayComments(); ?>
				</div>
			</div>
	</div>

	<script src="../inc/article.js"></script>
<?php require_once('../inc/footer.inc.php'); ?>
