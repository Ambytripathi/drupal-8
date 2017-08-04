<?php
/**
 * @file
 * Contains \Drupal\amazing_forms\Form\PaymentForm.
 */

namespace Drupal\user_details\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\user\UserInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Component\Utility\Crypt;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\std_hacks\Controller\APIController;

/**
 * Payment form.
 */
class ChangepasswordForm extends FormBase {
	
  /**
   * {@inheritdoc}
   */
   
   /**
   * The account the shortcut set is for.
   *
   * @var \Drupal\user\UserInterface
   */
  //protected $user;

  /**
   * The Password Hasher.
   *
   * @var \Drupal\Core\Password\PasswordInterface;
   */
  protected $password_hasher;

  /**
   * Constructs a UserPasswordForm object.
   *
   * @param \Drupal\Core\Password $password_hasher
   *   The password hasher.
   * @param \Drupal\Core\Session $account
   *   The account.
   */
  
  public function __construct(PasswordInterface $password_hasher, AccountInterface $account) {
    $this->password_hasher = $password_hasher;
    $this->account = $account;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('password'),
      $container->get('current_user')
    );
  }
   
  public function getFormId() {
	   
    return 'user_details_changepassword_form';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

	if (\Drupal::currentUser()->isAnonymous()) {
	  // Anonymous user...
	  return $form; // Or drupal_access_denied()?
	}

    // Get the currently logged in user object.
	$form['#account'] = $GLOBALS['user'];
	
    $form['change_your_password'] = array(
		'#type' => 'fieldset',
		'#title' => t('Change your password'),
	);
	$form['change_your_password']['current_pass'] = array(
		'#type' => 'password',
		'#title' => t('Current password'),
		'#size' => 25,
		'#required' => TRUE
	  );

	  // Password confirm field.
	$form['change_your_password']['account']['pass'] = array(
		'#type' => 'password_confirm',
		'#size' => 25,
		//~ '#title' => t('New Password'),
		'#required' => TRUE
	);
	$form['markup_mandatory'] = array('#markup' => "<p class='text-danger'> * denotes mandatory fields </p>");
	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Change your password'),
    '#attributes' => array("class"=>array("btn btn-success")),
	);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	$current_pass_input = trim($form_state->getValue('current_pass'));
    if ($current_pass_input) {
      $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      if (!$this->password_hasher->check($current_pass_input, $user->getPassword())) {
        $form_state->setErrorByName('current_pass', $this->t('The current password you provided is incorrect.'));
      }
    }
  }

   
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
  	    global $base_url;
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		$user->setPassword($form_state->getValue('pass'));
		$user->save();
		drupal_set_message($this->t('Your password has been changed.'));
		$uid = \Drupal::currentUser()->id();
		$squery = db_select('custom_users_information','cu');
		$squery->fields('cu');
		$squery->condition('user_id',$uid,'=');
		$sresult = $squery->execute();
		$sdata = $sresult->fetchObject();
		$first_name=$sdata->first_name;
		$last_name=$sdata->last_name;
		$query = db_select('custom_users','c');
		$query->fields('c');
		$query->condition('id',$uid,'=');
		$result = $query->execute();
		$data = $result->fetchObject();
		$mail=$data->email;
		$msg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:720px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($first_name).' '.ucfirst($last_name).",<br /><br />Your password change is successful. <br> Please log in to <a href='$base_url/user/login' >your account </a> with the new password.<br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
		//$msg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:880px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($first_name).' '.ucfirst($last_name).",<br /><br />Your password change is successful. <br> Please log in to <a href='$base_url/user/login' >your account </a> with the new password.<br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='text-align: center;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 24px;margin:20px 0;'>Download our apps:</p><div style='margin-bottom:20px;'><a href='#'><img src='$base_url/themes/explore/images/google.png'></a><a href='#'><img src='$base_url/themes/explore/images/apple.png'></a></div></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
		$input = array("from"=>"Ezplor <welcome@ezplor.com>","to"=>$mail,"subject"=>"Password change is successful!","category"=>"Change Password","msg"=>$msg);
		$classobj = new APIController();
		$mail_out= $classobj->global_mail_trigger($input);
   }   
}
?>
