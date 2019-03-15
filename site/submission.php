<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

function get_post($name, $default = '') {return strip_tags(utils_safe_post($name, $default));}
$db = new Database();
$config = $db->config();
$data = new Data($db);

//////////
$attention_message = '';
$attention_type = '';
if (!empty($_POST)) {
	$fields = array();
	$required_fields = array(
		'sex' => 'sex',
		'first_name' => 'first name',
        'last_name' => 'last name',
		'height' => 'height',
		'bust' => 'bust',
        'age' => 'age',
		'waist' => 'waist',
		'city' => 'city',
		'hips' => 'hips',
		'state' => 'state',
		'dress_size' => 'dress size',
        'country' => 'country',
		'shoe' => 'shoe size',
        'mobile' => 'mobile',
        'hair' => 'hair colour',
        'email' => 'email',
        'eyes' => 'eye colour',
        'message' => 'message',
	);
	$file_fields = array('file-1', 'file-2', 'file-3', 'file-4');
	foreach($required_fields as $field_name => $field_title) {
		$field_value = get_post($field_name, false);
		if ($field_value || $field_value === "0") {
			$fields[$field_name] = $field_value;
		} else {
			$attention_message = 'Missing field: '.$field_title;
			$attention_type = 'error';
		}
	}
	if (!$attention_type) {
		if (!array_key_exists('email', $fields) && !array_key_exists('mobile', $fields)) {
			$attention_message = 'Missing either email or mobile (phone).';
			$attention_type = 'error';
		} else if (array_key_exists('email', $fields) && !utils_valid_email($fields['email'])) {
			$attention_message = 'Invalid given email.';
			$attention_type = 'error';
		}
	}
	if (!$attention_type) {
		foreach ($file_fields as $file_field_name) {
			if(isset($_FILES[$file_field_name]) && $_FILES[$file_field_name]['name']) {
				$upload_folder = server_dir().'/uploads';
				if (!file_exists($upload_folder))
					mkdir($upload_folder);
				$ret = utils_upload($file_field_name, $upload_folder);
				$uploaded_file_path = $ret['file'];
				$error_message = $ret['error'];
				if($uploaded_file_path) {
					$fields[$file_field_name] = $uploaded_file_path;
				}
				else {
					$attention_message = 'Error when uploading file '.$file_field_name.': '.$error_message;
					$attention_type = 'error';
				}
			}
		}
	}
	if (!$attention_type) {
	    $message = htmlentities($fields['message']);
		$message = str_replace("\r\n", "<br/>", $message);
		$message = str_replace("\n", "<br/>", $message);
		$message = str_replace("\r", "<br/>", $message);
		$fields['message'] = $message;
    }
	if (!$attention_type) {
		$subject = 'DIVERSITY MONTREAL / Model Submission Request ('.date('d/m/Y - H:i:s').')';
		$body = '';
		$field_names_to_print = array(
			'sex',
			'first_name',
			'last_name',
			'age',
			'email',
			'mobile',
			'height',
			'bust',
			'waist',
			'hips',
			'dress_size',
			'shoe',
			'hair',
			'eyes',
			'city',
			'state',
			'country',
			'message',
		);
		$fields_titles = array(
			'age' => 'Age',
			'city' => 'City',
			'country' => 'Country',
			'dress_size' => 'Dress size',
			'first_name' => 'First name',
			'hair' => 'Hair colour',
			'height' => 'Height',
			'last_name' => 'Last name',
			'mobile' => 'Mobile phone',
			'sex' => 'Sex',
			'shoe' => 'Shoe size',
			'state' => 'State',
            'address' => 'Address',
            'bust' => 'Bust',
            'dress' => 'Dress',
            'email' => 'Email',
            'eyes' => 'Eye colour',
            'file-1' => 'Close-up (photo)',
            'file-2' => 'Waist-up (photo)',
            'file-3' => 'Full-length (photo)',
            'file-4' => 'Profile (photo)',
            'hairs' => 'Hair colour',
            'hips' => 'Hips',
            'waist' => 'Waist',
            'message' => 'Message',
		);
		capture_start();
		?>
        <div>
            <h1><?php echo $subject;?></h1>
            <table>
				<?php
				foreach($field_names_to_print as $field_name_to_print) {
					$title = $fields_titles[$field_name_to_print];
					$value = isset($fields[$field_name_to_print]) ? $fields[$field_name_to_print] : '(none)';
					?>
                    <tr><td valign="top"><strong><?php echo $title;?>:</strong></td><td><?php echo $value;?></td></tr>
					<?php
				}
				foreach($file_fields as $file_field_name) {
					if (isset($fields[$file_field_name])) {
						$file_url = str_replace(server_dir(), server_http(), $fields[$file_field_name]);
						?>
                        <tr><td><strong><?php echo $fields_titles[$file_field_name];?>:</strong></td><td><a href="<?php echo $file_url;?>"><?php echo $file_url;?></a></td></tr>
						<?php
					}
				}
				?>
            </table>
        </div>
		<?php
		capture_end($body);
		$sent = utils_mail($config->site_email(), $subject, $body);
		if ($sent) {
			$attention_message = 'Your application was correctly submitted. We will contact you soon. Thanks!';
			$attention_type = 'success';
		} else {
			$attention_message = 'Error while sending your request. Please retry later!';
			$attention_type = 'error';
		}
	}
}
//////////

$data->title = 'Submission | DMM';
$data->pagename = 'submission';
capture_start();
?>
<div class="submission container">
    <div class="px-5 mx-4">
        <div class="titled-outer text-center">
            <div class="titled-inner">
                <h1 class="text-center"><?php echo $config->submission_title();?></h1>
                <div class="titled-content-wrapper d-flex">
                    <div class="mt-4 text-justify flex-grow-1 square-text"><?php echo $config->submission_main_text();?></div>
                </div>
            </div>
        </div>
        <div class="section-details mt-5 pt-5">
            <div class="text-center mb-5 text-open-call">
                <div class="few-details"><div class="wrapper"><?php echo $config->submission_details();?></div></div>
                <div class="more-details py-4 px-1"><div class="wrapper"><?php echo $config->submission_more_details();?></div></div>
            </div>
            <div class="details text-right">
                <div><a href="#">details</a></div>
            </div>
        </div>
        <?php if($attention_message) { ?>
            <div class="mt-5 p-2 message-<?php echo $attention_type;?>"><?php echo $attention_message;?></div>
        <?php }; ?>
        <!-- formulaire -->
        <div class="my-5 pb-5">
            <form method="post" enctype="multipart/form-data">
                <div class="form-row selections mb-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="female" value="female" name="sex" <?php if (utils_s_post('sex') === 'female') {echo 'checked';} ?>>
                        <label class="custom-control-label" for="female">Femme</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="male" value="male" name="sex" <?php if (utils_s_post('sex') === 'male') {echo 'checked';} ?>>
                        <label class="custom-control-label" for="male">Homme</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="text" name="first_name" class="form-control" placeholder="First name" value="<?php echo utils_s_post('first_name', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="height" class="form-control" placeholder="Height" value="<?php echo utils_s_post('height', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="text" name="last_name" class="form-control" placeholder="Last name" value="<?php echo utils_s_post('last_name', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="bust" class="form-control" placeholder="Bust" value="<?php echo utils_s_post('bust', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="number" name="age" class="form-control" placeholder="Age" value="<?php echo utils_s_post('age', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="waist" class="form-control" placeholder="Waist" value="<?php echo utils_s_post('waist', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <input type="text" name="city" class="form-control" placeholder="City" value="<?php echo utils_s_post('city', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="hips" class="form-control" placeholder="Hips" value="<?php echo utils_s_post('hips', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <input type="text" name="state" class="form-control" placeholder="STATE" value="<?php echo utils_s_post('state', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="dress_size" class="form-control" placeholder="Dress size" value="<?php echo utils_s_post('dress_size', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="text" name="country" class="form-control" placeholder="COUNTRY" value="<?php echo utils_s_post('country', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="shoe" class="form-control" placeholder="Shoe" value="<?php echo utils_s_post('shoe', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="text" name="mobile" class="form-control" placeholder="mobile" value="<?php echo utils_s_post('mobile', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="hair" class="form-control" placeholder="Hair" value="<?php echo utils_s_post('hair', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo utils_s_post('email', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="text" name="eyes" class="form-control" placeholder="Eye colour" value="<?php echo utils_s_post('eyes', '');?>"/>
                    </div>
                </div>
                <div class="my-5"><?php echo $config->submission_form_text_right();?></div>
                <div class="mt-4 mb-2 info">IMAGES (UP TO 2MB EACH)</div>
                <div class="form-row files align-items-center text-center">
                    <div class="form-group col-sm">
                        <div>CLOSE-UP</div>
                        <div class="example-image" <?php if (utils_submission_demo_photo_1()) { ?>style="background-image: url('<?php echo utils_as_link(utils_submission_demo_photo_1()); ?>');"<?php } ?>>
                            <label class="button btn btn-outline-dark">
                                <span id="file-1">upload</span> <input type="file" name="file-1" hidden onchange="displayLabelFile(event, 'file-1');"/>
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-sm">
                        <div>WAIST-UP</div>
                        <div class="example-image" <?php if (utils_submission_demo_photo_2()) { ?>style="background-image: url('<?php echo utils_as_link(utils_submission_demo_photo_2()); ?>');"<?php } ?>>
                            <label class="button btn btn-outline-dark">
                                <span id="file-2">upload</span> <input type="file" name="file-2" hidden onchange="displayLabelFile(event, 'file-2');"/>
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-sm">
                        <div>FULL LENGTH</div>
                        <div class="example-image" <?php if (utils_submission_demo_photo_3()) { ?>style="background-image: url('<?php echo utils_as_link(utils_submission_demo_photo_3()); ?>');"<?php } ?>>
                            <label class="button btn btn-outline-dark">
                                <span id="file-3">upload</span> <input type="file" name="file-3" hidden onchange="displayLabelFile(event, 'file-3');"/>
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-sm">
                        <div>PROFILE</div>
                        <div class="example-image" <?php if (utils_submission_demo_photo_4()) { ?>style="background-image: url('<?php echo utils_as_link(utils_submission_demo_photo_4()); ?>');"<?php } ?>>
                            <label class="button btn btn-outline-dark">
                                <span id="file-4">upload</span> <input type="file" name="file-4" hidden onchange="displayLabelFile(event, 'file-3');"/>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p><?php echo $config->submission_form_message_desc();?></p>
                    <div class="form-row">
                        <div class="form-group col">
                            <textarea class="form-control textarea-message" name="message" placeholder="Message"><?php echo utils_s_post('message', '');?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3 submit-button">
                    <button type="submit" class="button btn btn-lg btn-outline-dark btn-block">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
capture_end($data->content);
capture_start();
?>
<script>//<!--
    function displayLabelFile(event, labelID) {
        const element = document.getElementById(labelID);
        element.classList.remove('strong');
        if (element) {
            element.textContent = 'Drop file here to upload';
            const fileName = event.target.value;
            const startBaseName = fileName.includes('\\') ? fileName.lastIndexOf('\\') : fileName.lastIndexOf('/');
            if (startBaseName >= 0 && startBaseName < fileName.length - 1) {
                const baseName = fileName.substring(startBaseName + 1);
                element.textContent = `File: ${baseName}`;
                element.classList.add('strong');
            }
        }
    }
    $(document).ready(function () {
        const sectionDetails = $('.section-details');
        const textOpenCall = $('.text-open-call');
        const detailsLink = $('.details a');
        const fewDetails = $('.few-details');
        const moreDetails = $('.more-details');
        function resizeTextOpenCall() {
            let fewDetailsHeightString = fewDetails.css('height');
            let moreDetailsHeightString = moreDetails.css('height');
            const fewDetailsHeight = parseInt(fewDetailsHeightString.replace('px', ''));
            const moreDetailsHeight = parseInt(moreDetailsHeightString.replace('px', ''));
            // console.log(`few details: ${fewDetailsHeight}`);
            // console.log(`more details: ${moreDetailsHeight}`);
            textOpenCall.css('height', `${fewDetailsHeight > moreDetailsHeight ? fewDetailsHeight : moreDetailsHeight}px`);
        }
        resizeTextOpenCall();
        $(window).resize(resizeTextOpenCall);
        detailsLink.mouseenter(function () {
            sectionDetails.addClass('show-more');
        });
        sectionDetails.mouseleave(function () {
            sectionDetails.removeClass('show-more');
        });
        detailsLink.click(function () {
            return false;
        })
    })
//--></script>
<?php
capture_end($data->scripts);
echo template($data);
?>

