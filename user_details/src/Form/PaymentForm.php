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
use Drupal\std_hacks\Controller\APIController;
use Drupal\Core\Url;
/**
 * Payment form.
 */
class PaymentForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
     
    return 'user_details_payment_form';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	$form['#attached']['library'][] = 'user_details/user-details';
  $user_uid = \Drupal::currentUser()->id();
  $query = db_select('custom_users_information','cui');
    $query->fields('cui', ['id', 'payment_method',  'bank_name', 'bank_code', 'bank_address', 'bank_branch_code', 'bank_branch', 'bank_swift_code', 'account_number', 'account_first_name', 'account_last_name', 'account_holder_city', 'account_holder_address', 'account_holder_phone','account_holder_country_code','paypal_email','refer_management_approval']);
    $query->condition('id', $user_uid);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
    }
    
    $querys = db_select('custom_country','cc');
    $querys->fields('cc', ['id', 'country_name','description','country_code','phone_code']);
    $results = $querys->execute();
    $datas = $results->fetchAll();
    $cntry_code = array();
    foreach ($datas as $values) {
      $cntry_code[$values->id] = $values->country_code;
        $phone[$values->phone_code] = $values->phone_code;
    }
    

    
    $user = \Drupal::currentUser()->getRoles();
  if(in_array("guest", $user)) {
    return $form; 
  }
  
  if($value->bank_name == ''){
    $disabled = FALSE;
  }
  else{
    $disabled = TRUE;
  }
  
  if($value->payment_method == ''){
    $disableds = FALSE;
  }
  else{
    $disableds = TRUE;
  }
  
  if($value->paypal_email == ''){
    $disabledemail = FALSE;
  }
  else{
    $disabledemail = TRUE;
  }
  
  $userquery = db_select('custom_users','cu');
  $userquery->fields('cu', ['id', 'country_code', 'phone_number', 'email']);
  $userquery->condition('id', $user_uid);
  $userresult = $userquery->execute();
  $userdata = $userresult->fetchAll();
  foreach ($userdata as $uservalue) {
  }
  $query1 = db_select('custom_users_information','cui');
  $query1->fields('cui');
  $query1->condition('user_id', $user_uid);
  $result1 = $query1->execute();
  $data1 = $result1->fetchObject();
  $country_user=$data1->country;
  $query2 = db_select('custom_country','cc');
  $query2->fields('cc');
  $query2->condition('id', $country_user);
  $result2 = $query2->execute();
  $data2 = $result2->fetchObject();
  $countryname_user=$data2->country_name;
  $form['markup'] = array(
    '#markup' => '<h4>How can we pay you?</h4>
      <p>We request for your bank details to make the necessary payments upon service(s) rendered. No cash payment is allowed to be collected from traveler- as payments to and from all parties are done via Ezplor. All payments made will be denominated in the currency of Singapore Dollar (SGD).</p>
      <p>Please take note that you can only enter the payment details once. Should any edits are required, please contact the Ezplor team at payment@ezplor.com for further assistance. </p>'
  );
  //~ print '<pre>'; print_r(); exit;
  $form['field_payment_methods'] = array(
    '#type' => 'radios',
    '#title' => t('Payment Methods'),
    '#options' => array(
          'bank_transfer' => t('Bank transfer'),
          'paypal' => t('Paypal'),),
    '#default_value' => $value->payment_method,
    '#attributes' => array('disabled' => $disableds,),
    '#required' => TRUE,
  );  
  
  $form['field_paypal_emailid'] = array(
      '#type' => 'textfield',
      '#title' => t('Paypal Email Id'),
      '#default_value' =>$value->paypal_email,
      '#attributes' => array('disabled' => $disabledemail,),
      '#states' => array(
         'visible' => array(
       ':input[name="field_payment_methods"]' => array('value' => 'paypal'),
     ),
     'required' => array(
       ':input[name="field_payment_methods"]' => array('value' => 'paypal'),
    ),
      ),
    );
  
  
  if($countryname_user=="Malaysia")
  {
    $form['field_bank_name'] = array(
        '#type' => 'select',
        '#title' => t('Bank Name'),
        '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('-Please Select Bank-'),
        '#options' => array(
            'CIMB Bank Berhad' => t('CIMB Bank Berhad'),
            'Maybank (Malayan Banking Berhad)' => t('Maybank (Malayan Banking Berhad)'),
            'Citibank Berhad' => t('Citibank Berhad'),
            'Hong Leong Bank Berhad' => t('Hong Leong Bank Berhad'),
            'HSBC Bank Malaysia Berhad' => t('HSBC Bank Malaysia Berhad'),
            'OCBC Bank (Malaysia) Berhad' => t('OCBC Bank (Malaysia) Berhad'),
            'Public Bank Berhad' => t('Public Bank Berhad'),
            'RHB Bank Berhad' => t('RHB Bank Berhad'),
            'Standard Chartered Bank Malaysia Berhad' => t('Standard Chartered Bank Malaysia Berhad'),
            'Bank Rakyat' => t('Bank Rakyat'),
            'AmBank (M) Berhad' => t('AmBank (M) Berhad'),
            'Affin Bank Berhad' => t('Affin Bank Berhad'),
            'Alliance Bank Malaysia Berhad' => t('Alliance Bank Malaysia Berhad'),
            'AMMB Holdings' => t('AMMB Holdings'),
            'Bangkok Bank Berhad' => t('Bangkok Bank Berhad'),
            'Bank of America Malaysia Berhad' => t('Bank of America Malaysia Berhad'),
            'Bank of China (Malaysia) Berhad' => t('Bank of China (Malaysia) Berhad'),
            'Bank of Tokyo-Mitsubishi UFJ (Malaysia) Berhad' => t('Bank of Tokyo-Mitsubishi UFJ (Malaysia) Berhad'),
            'BNP Paribas Malaysia Berhad' => t('BNP Paribas Malaysia Berhad'),
            'China Construction Bank (Malaysia) Berhad' => t('China Construction Bank (Malaysia) Berhad'),
            'Deutsche Bank (Malaysia) Berhad' => t('Deutsche Bank (Malaysia) Berhad'),
            'India International Bank (Malaysia) Berhad' => t('India International Bank (Malaysia) Berhad'),
            'Industrial and Commercial Bank of China (Malaysia) Berhad' => t('Industrial and Commercial Bank of China (Malaysia) Berhad'),
            'J.P. Morgan Chase Bank Berhad' => t('J.P. Morgan Chase Bank Berhad'),
            'Mizuho Bank (Malaysia) Berhad' => t('Mizuho Bank (Malaysia) Berhad'),
            'National Bank of Abu Dhabi Malaysia Berhad' => t('National Bank of Abu Dhabi Malaysia Berhad'),
            'Sumitomo Mitsui Banking Corporation Malaysia Berhad' => t('Sumitomo Mitsui Banking Corporation Malaysia Berhad'),
            'The Bank of Nova Scotia Berhad' => t('The Bank of Nova Scotia Berhad'),
            'The Royal Bank of Scotland Berhad' => t('The Royal Bank of Scotland Berhad'),
            'United Overseas Bank (Malaysia) Bhd.' => t('United Overseas Bank (Malaysia) Bhd.'),
            'EON Bank Berhad' => t('EON Bank Berhad'),
            'Affin Islamic Bank Berhad' => t('Affin Islamic Bank Berhad'),
            'Al Rajhi Banking & Investment Corporation (Malaysia) Berhad' => t('Al Rajhi Banking & Investment Corporation (Malaysia) Berhad'),
            'Alliance Islamic Bank Berhad' => t('Alliance Islamic Bank Berhad'),
            'AmIslamic Bank Berhad' => t('AmIslamic Bank Berhad'),
            'Asian Finance Bank Berhad' => t('Asian Finance Bank Berhad'),
            'Bank Islam Malaysia Berhad' => t('Bank Islam Malaysia Berhad'),
            'Bank Muamalat Malaysia Berhad' => t('Bank Muamalat Malaysia Berhad'),
            'CIMB Islamic Bank Berhad' => t('CIMB Islamic Bank Berhad'),
            'HSBC Amanah Malaysia Berhad' => t('HSBC Amanah Malaysia Berhad'),
            'Hong Leong Islamic Bank Berhad' => t('Hong Leong Islamic Bank Berhad'),
            'Kuwait Finance House (Malaysia) Berhad' => t('Kuwait Finance House (Malaysia) Berhad'),
            'Maybank Islamic Berhad' => t('Maybank Islamic Berhad'),
            'OCBC Al-Amin Bank Berhad' => t('OCBC Al-Amin Bank Berhad'),
            'Public Islamic Bank Berhad' => t('Public Islamic Bank Berhad'),
            'RHB Islamic Bank Berhad' => t('RHB Islamic Bank Berhad'),
            'Standard Chartered Saadiq Berhad' => t('Standard Chartered Saadiq Berhad')
            
        ),
        '#default_value' => $value->bank_name,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
         ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
  }
  elseif($countryname_user=="Philippines")
  {
    $form['field_bank_name'] = array(
        '#type' => 'select',
        '#title' => t('Bank Name'),
        '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('-Please Select Bank-'),
        '#options' => array(
            'ABN AMRO SAVINGS BANK CORPORATION' => t('ABN AMRO SAVINGS BANK CORPORATION'),
            'AIG PHILAM SAVINGS BANK, INC' => t('AIG PHILAM SAVINGS BANK, INC'),
            'ALLIED BANKING CORPORATION' => t('ALLIED BANKING CORPORATION'),
            'AMERICAN EXPRESS' => t('AMERICAN EXPRESS'),
            'ASIA UNITED BANK' => t('ASIA UNITED BANK'),
            'ASIANBANK CORPORATION' => t('ASIANBANK CORPORATION'),
            'AUSTRALIA AND NEW ZEALAND BANKING GROUP LTD.' => t('AUSTRALIA AND NEW ZEALAND BANKING GROUP LTD.'),
            'BANCO DE ORO UNIVERSAL BANK' => t('BANCO DE ORO UNIVERSAL BANK'),
            'BANK OF AMERICA N.T. AND S.A.' => t('BANK OF AMERICA N.T. AND S.A.'),
            'BANK OF THE PHILIPPINE ISLANDS' => t('BANK OF THE PHILIPPINE ISLANDS'),
            'BANKARD, INC.' => t('BANKARD, INC.'),
            "BULL'S EYE CREDIT UNION" => t("BULL'S EYE CREDIT UNION"),
            'CHINATRUST (PHILIPPINES) COMMERCIAL BANK' => t('CHINATRUST (PHILIPPINES) COMMERCIAL BANK'),
            'CITIBANK SAVINGS INC.' => t('CITIBANK SAVINGS INC.'),
            'CITIBANK, N.A.' => t('CITIBANK, N.A.'),
            'CITICORP' => t('CITICORP'),
            'EAST WEST BANKING CORPORATION' => t('EAST WEST BANKING CORPORATION'),
            'EQUICOM SAVINGS BANK INC.' => t('EQUICOM SAVINGS BANK INC.'),
            'EQUITABLE BANKING CORPORATION' => t('EQUITABLE BANKING CORPORATION'),
            'EQUITABLE PCI BANK' => t('EQUITABLE PCI BANK'),
            'FIRST E-BANK CORPORATION' => t('FIRST E-BANK CORPORATION'),
            'HONGKONG AND SHANGHAI BANKING CORPORATION, LTD.' => t('HONGKONG AND SHANGHAI BANKING CORPORATION, LTD.'),
            'HSBC / HSBC PREMIER' => t('HSBC / HSBC PREMIER'),
            'HSBC PHILIPPINE AIRLINES MABUHAY MILES' => t('HSBC PHILIPPINE AIRLINES MABUHAY MILES'),
            'HSBC SAVINGS BANK (PHILIPPINES), INC' => t('HSBC SAVINGS BANK (PHILIPPINES), INC'),
        	'LANDBANK OF THE PHILIPPINES' => t('LANDBANK OF THE PHILIPPINES'),
            'MAYBANK' => t('MAYBANK'),
            'METROBANK CARD CORPORATION' => t('METROBANK CARD CORPORATION'),
            'METROPOLITAN BANK AND TRUST COMPANY' => t('METROPOLITAN BANK AND TRUST COMPANY'),
            'MICROFINANCE MAXIMUM SAVINGS BANK, INC.' => t('MICROFINANCE MAXIMUM SAVINGS BANK, INC.'),
            'ONECARD COMPANY, INC' => t('ONECARD COMPANY, INC'),
            'PHILAM SAVINGS BANK' => t('PHILAM SAVINGS BANK'),
            'PHILIPPINE COMMERCIAL INTERNATIONAL BANK' => t('PHILIPPINE COMMERCIAL INTERNATIONAL BANK'),
            'RIZAL COMMERCIAL BANKING CORPORATION' => t('RIZAL COMMERCIAL BANKING CORPORATION'),
            'SECURITY BANK CORPORATION' => t('SECURITY BANK CORPORATION'),
            'STANDARD CHARTERED BANK' => t('STANDARD CHARTERED BANK'),
            'THE HONGKONG AND SHANGHAI BANKING CORPORATION LIMITED' => t('THE HONGKONG AND SHANGHAI BANKING CORPORATION LIMITED'),
            'UNIBANCARD CORPORATION' => t('UNIBANCARD CORPORATION'),
            'UNION BANK OF THE PHILIPPINES' => t('UNION BANK OF THE PHILIPPINES'),
            'UNITED OVERSEAS BANK PHILIPPINES' => t('UNITED OVERSEAS BANK PHILIPPINES'),
        	
        ),
        '#default_value' => $value->bank_name,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
         ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
  }
  elseif($countryname_user=="Thailand")
  {
    $form['field_bank_name'] = array(
        '#type' => 'select',
        '#title' => t('Bank Name'),
        '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('-Please Select Bank-'),
        '#options' => array(
            'AEON THANA SINSAP (THAILAND) PUBLIC COMPANY LIMITED' => t('AEON THANA SINSAP (THAILAND) PUBLIC COMPANY LIMITED'),
            'AIG Card (THAILAND) COMPANY LIMITED' => t('AIG Card (THAILAND) COMPANY LIMITED'),
            'Ayudhya Card Services Co.,Ltd.' => t('Ayudhya Card Services Co.,Ltd.'),
            'BANGKOK BANK PUBLIC COMPANY LIMITED' => t('BANGKOK BANK PUBLIC COMPANY LIMITED'),
            'BANK OF AYUDHYA PUBLIC COMPANY LIMITED' => t('BANK OF AYUDHYA PUBLIC COMPANY LIMITED'),
            'CITIBANK, N.A.' => t('CITIBANK, N.A.'),
            'GE CAPITAL (THAILAND) LIMITED' => t('GE CAPITAL (THAILAND) LIMITED'),
            'GENERAL CARD SERVICES LIMITED' => t('GENERAL CARD SERVICES LIMITED'),
            'KASIKORNBANK PUBLIC COMPANY LIMITED' => t('KASIKORNBANK PUBLIC COMPANY LIMITED'),
            'KRUNG THAI BANK PUBLIC COMPANY LIMITED' => t('KRUNG THAI BANK PUBLIC COMPANY LIMITED'),
            'KRUNGSRIAYUDHYA CARD CO., LTD.' => t('KRUNGSRIAYUDHYA CARD CO., LTD.'),
            "KRUNGTHAI CARD PUBLIC COMPANY LIMITED" => t("KRUNGTHAI CARD PUBLIC COMPANY LIMITED"),
            'PAYMENT SOLUTION COMPANY LIMITED' => t('PAYMENT SOLUTION COMPANY LIMITED'),
            'STANDARD CHARTERED BANK (THAI) PUBLIC COMPANY LIMITED' => t('STANDARD CHARTERED BANK (THAI) PUBLIC COMPANY LIMITED'),
            'THANACHART BANK PUBLIC COMPANY LIMITED' => t('THANACHART BANK PUBLIC COMPANY LIMITED'),
            'THE GOVERNMENT SAVINGS BANK' => t('THE GOVERNMENT SAVINGS BANK'),
            'THE HONGKONG AND SHANGHAI BANKING CORPORATION LIMITED' => t('THE HONGKONG AND SHANGHAI BANKING CORPORATION LIMITED'),
            'EQUICOM SAVINGS BANK INC.' => t('EQUICOM SAVINGS BANK INC.'),
            'EQUITABLE BANKING CORPORATION' => t('EQUITABLE BANKING CORPORATION'),
            'EQUITABLE PCI BANK' => t('EQUITABLE PCI BANK'),
            'FIRST E-BANK CORPORATION' => t('FIRST E-BANK CORPORATION'),
            'HONGKONG AND SHANGHAI BANKING CORPORATION, LTD.' => t('HONGKONG AND SHANGHAI BANKING CORPORATION, LTD.'),
            'THE SIAM COMMERCIAL BANK PUBLIC COMPANY LIMITED' => t('THE SIAM COMMERCIAL BANK PUBLIC COMPANY LIMITED'),
            'TMB BANK PUBLIC COMPANY LIMITED' => t('TMB BANK PUBLIC COMPANY LIMITED'),
            'UNITED OVERSEAS BANK BANK (THAI) PUBLIC COMPANY LIMITED' => t('UNITED OVERSEAS BANK BANK (THAI) PUBLIC COMPANY LIMITED')
        ),
        '#default_value' => $value->bank_name,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
         ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
  }
  elseif($countryname_user=="Indonesia")
  {
    $form['field_bank_name'] = array(
        '#type' => 'select',
        '#title' => t('Bank Name'),
        '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('-Please Select Bank-'),
        '#options' => array(
            'Central bank' => t('Central bank'),
            'Bank Indonesia' => t('Bank Indonesia)'),
            'Bank Mandiri' => t('Bank Mandiri'),
            'Bank Negara Indonesia' => t('Bank Negara Indonesia'),
            'Bank Ekspor Indonesia' => t('Bank Ekspor Indonesia'),
            'Bank Rakyat Indonesia' => t('Bank Rakyat Indonesia'),
            'Bank Tabungan Negara' => t('Bank Tabungan Negara'),
            'Bank Agro Niaga' => t('Bank Agro Niaga'),
            'Bank Artha Graha Internasional' => t('Bank Artha Graha Internasional'),
            'Bank Bumiputera Indonesia' => t('Bank Bumiputera Indonesia'),
            'Bank Capital Indonesia' => t('Bank Capital Indonesia'),
            'Bank Central Asia' => t('Bank Central Asia'),
            'Bank CIMB Niaga' => t('Bank CIMB Niaga'),
            'Bank UOB Buana' => t('Bank UOB Buana'),
            'Bank Bukopin' => t('Bank Bukopin'),
            'Bank Danamon' => t('Bank Danamon'),
            'Bank Eksekutif International' => t('Bank Eksekutif International'),
            'Bank Kesawan' => t('Bank Kesawan'),
            'Bank Bumi Arta' => t('Bank Bumi Arta'),
            'Bank International Indonesia' => t('Bank International Indonesia'),
            'Bank OCBC NISP' => t('Bank OCBC NISP'),
            'Bank Permata' => t('Bank Permata'),
            'Bank Victoria International' => t('Bank Victoria International'),
            'Bank Mayapada' => t('Bank Mayapada'),
            'Bank Mega' => t('Bank Mega'),
            'Bank Multicor' => t('Bank Multicor'),
            'Bank Mutiara' => t('Bank Mutiara'),
            'Bank Pan Indonesia' => t('Bank Pan Indonesia'),
            'Bank sinarmas' => t('Bank sinarmas'),
            'Bank Himpunan Saudara 1906' => t('Bank Himpunan Saudara 1906'),
            'Bank Prima Master' => t('Bank Prima Master'),
            'Bank Ekonomi Raharja' => t('Bank Ekonomi Raharja'),
            'Bank Ganesha' => t('Bank Ganesha'),
            'Bank Agroniaga' => t('Bank Agroniaga'),
            'Bank Antardaerah' => t('Bank Antardaerah'),
            'Bank Bisnis Internasional' => t('Bank Bisnis Internasional'),
            'Bank IFI' => t('Bank IFI'),
            'Bank Mestika Dharma' => t('v'),
            'Bank Sinar Harapan Bali (Merger pending with Bank Mandiri)' => t('Bank Sinar Harapan Bali (Merger pending with Bank Mandiri)'),
            'Bank Anglomas Internasional (Merger pending with Wishart)' => t('Bank Anglomas Internasional (Merger pending with Wishart)'),
            'Bank Royal Indonesia' => t('Bank Royal Indonesia'),
            'Bank Alfindo' => t('Bank Alfindo'),
            'Bank Artos Indonesia' => t('Bank Artos Indonesia'),
            'Bank Bintang Manunggal' => t('Bank Bintang Manunggal'),
            'Bank Mitraniaga' => t('Bank Mitraniaga'),
            'Bank Kesejahteraan Ekonomi' => t('Bank Kesejahteraan Ekonomi'),
            'Bank Fama Internasional' => t('Bank Fama Internasional'),
            'Bank Harda Internasional' => t('Bank Harda Internasional'),
            'Bank CIC' => t('Bank CIC'),
            'Bank Harmoni International(Merger pending with Bank Index Selindo)' => t('Bank Harmoni International(Merger pending with Bank Index Selindo)'),
            'Bank Ina Perdana' => t('Bank Ina Perdana'),
            'Bank Maspion' => t('Bank Maspion'),
            'Bank Jasa Arta' => t('Bank Jasa Arta'),
            'Bank Jasa Jakarta' => t('Bank Jasa Jakarta'),
            'Bank Index Selindo (Merger pending with Bank Harmoni International)' => t('Bank Index Selindo (Merger pending with Bank Harmoni International)'),
            'Bank Mayora' => t('Bank Mayora'),
            'Bank Harfa (Merger pending with Bank Panin)' => t('Bank Harfa (Merger pending with Bank Panin)'),
            'Bank Multi Arta Sentosa' => t('Bank Multi Arta Sentosa'),
            'Bank Persyarikatan Indonesia' => t('Bank Persyarikatan Indonesia'),
            'Bank Purba Danarta' => t('Bank Purba Danarta'),
            'Bank Akita' => t('Bank Akita'),
            'Bank Metro Express' => t('Bank Metro Express'),
            'Bank Sri Partha (Merger pending with Mercy Corp.)' => t('Bank Sri Partha (Merger pending with Mercy Corp.)'),
            'Bank Swaguna' => t('Bank Swaguna'),
            'Bank Tabungan Pensiunan Nasional' => t('Bank Tabungan Pensiunan Nasionalk'),
            'Bank Dipo International' => t('Bank Dipo International'),
            'Bank Yudha Bhakti' => t('Bank Yudha Bhakti'),
            'Bank Centratama Nasional' => t('Bank Centratama Nasional'),
            'Bank Liman International' => t('Bank Liman International'),
            'Bank UIB' => t('Bank UIB'),
            'Bank Arta Niaga Kencana (Merged with Commonwealth Bank of Australia)' => t('Bank Arta Niaga Kencana (Merged with Commonwealth Bank of Australia)'),
            'Bank Halim Indonesia (Merged with ICBC)' => t('Bank Halim Indonesia (Merged with ICBC)'),
            'Bank Haga (Merged with Rabobank)' => t('Bank Haga (Merged with Rabobank)'),
            'Bank Hagakita (Merged with Rabobank)' => t('Bank Hagakita (Merged with Rabobank)'),
            'Bank Indomonex (Acquired by Bank of India and State Bank of India)' => t('Bank Indomonex (Acquired by Bank of India and State Bank of India)'),
            'Bank Swadesi (Acquired by Bank of India and State Bank of India)' => t('Bank Swadesi (Acquired by Bank of India and State Bank of India)'),
            'Bank Nusantara Parahyangan (Acquired by consortium of Acom and Bank of Tokyo Mitsubishi UFJ)' => t('Bank Nusantara Parahyangan (Acquired by consortium of Acom and Bank of Tokyo Mitsubishi UFJ)'),
            'Bank Windu Kentjana (Merged with Bank Multicor)' => t('Bank Windu Kentjana (Merged with Bank Multicor)'),
            'Bank Muamalat Indonesia' => t('Bank Muamalat Indonesia'),
            'Bank Syariah Mega Indonesia' => t('Bank Syariah Mega Indonesia'),
            'Bank Syariah Mandiri' => t('Bank Syariah Mandiri')           
        ),
        '#default_value' => $value->bank_name,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
         ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
  }
  elseif($countryname_user=="Singapore")
  {
    $form['field_bank_name'] = array(
        '#type' => 'select',
        '#title' => t('Bank Name'),
        '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('-Please Select Bank-'),
        '#options' => array(
            'DBS Singapore' => t('DBS Singapore'),
            'UOB Singapore' => t('UOB Singapore'),
            'Citibank Singapore' => t('Citibank Singapore'),
            'Maybank Singapore' => t('Maybank Singapore'),
            'Standard Chartered Singapore' => t('Standard Chartered Singapore'),
            'SBI Singapore' => t('SBI Singapore'),
            'Bangkok bank Singapore' => t('Bangkok bank Singapore'),
            'CIMB Bank Singapore' => t('CIMB Bank Singapore'),
            'ICICI Singapore Bank' => t('ICICI Singapore Bank'),
            'RHB Singapore Bank' => t('RHB Singapore Bank'),
            'Bank of India Singapore' => t('Bank of India Singapore'),
            'ANZ Singapore' => t('ANZ Singapore'),
            'J.P. Morgan Singapore' => t('J.P. Morgan Singapore'),
            'HSBC Singapore' => t('HSBC Singapore'),
            'Hong Leong Finance' => t('Hong Leong Finance'),
            'BNP Paribas Singapore' => t('BNP Paribas Singapore'),
            'OCBC Singapore' => t('OCBC Singapore'),
            'Bank of Singapore' => t('Bank of Singapore'),
            'Islamic Bank of Asia' => t('Islamic Bank of Asia'),
        ),
        '#default_value' => $value->bank_name,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
         ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
  }
  elseif($countryname_user=="India")
  {
  	$form['field_bank_name'] = array(
  			'#type' => 'select',
  			'#title' => t('Bank Name'),
  			'#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
  			'#suffix' => '</div>',
  			'#empty_option' => $this->t('-Please Select Bank-'),
  			'#options' => array(
  					'Allahabad Bank' => t('Allahabad Bank'),
  					'Andhra Bank' => t('Andhra Bank'),
  					'Bank of India' => t('Bank of India'),
  					'Bank of Baroda' => t('Bank of Baroda'),
  					'Bank of Maharashtra' => t('Bank of Maharashtra'),
  					'Canara Bank' => t('Canara Bank'),
  					'Central Bank of India' => t('Central Bank of India'),
  					'Corporation Bank' => t('Corporation Bank'),
  					'Dena Bank' => t('Dena Bank'),
  					'Indian Bank' => t('Indian Bank'),
  					'Indian Overseas Bank' => t('Indian Overseas Bank'),
  					'IDBI Bank' => t('IDBI Bank'),
  					'Oriental Bank of Commerce' => t('Oriental Bank of Commerce'),
  					'Punjab & Sind Bank' => t('Punjab & Sind Bank'),
  					'Punjab National Bank' => t('Punjab National Bank'),
  					'State Bank Of India' => t('State Bank Of India'),
  					'Syndicate Bank' => t('Syndicate Bank'),
  					'UCO Bank' => t('UCO Bank'),
  					'Union Bank of India' => t('Union Bank of India'),
  					'United Bank of India' => t('United Bank of India'),
  					'Vijaya Bank' => t('Vijaya Bank'),
  					'Axis bank' => t('Axis bank'),
  					'Bandhan Bank' => t('Bandhan Bank'),
  					'Catholic Syrian Bank' => t('Catholic Syrian Bank'),
  					'City Union Bank' => t('City Union Bank'),
  					'DCB Bank' => t('DCB Bank'),
  					'Dhanlaxmi Bank' => t('Dhanlaxmi Bank'),
  					'Federal Bank' => t('Federal Bank'),
  					'HDFC Bank' => t('HDFC Bank'),
  					'ICICI Bank' => t('ICICI Bank'),
  					'IDFC Bank' => t('IDFC Bank'),
  					'Indus Ind Bank' => t('Indus Ind Bank'),
  					'Jammu and Kashmir Bank' => t('Jammu and Kashmir Bank'),
  					'Karnataka Bank' => t('Karnataka Bank'),
  					'Karur Vysya Bank' => t('Karur Vysya Bank'),
  					'Kotak Mahindra Bank' => t('Kotak Mahindra Bank'),
  					'Lakshmi Vilas Bank' => t('Lakshmi Vilas Bank'),
  					'Nainital Bank' => t('Nainital Bank'),
  					'RBL Bank' => t('RBL Bank'),
  					'South Indian Bank' => t('South Indian Bank'),
  					'Tamilnad Mercantile Bank' => t('Tamilnad Mercantile Bank'),
  					'Yes Bank' => t('Yes Bank'),
  					'AU Small Finance Bank' => t('AU Small Finance Bank'),
  					'Equitas Small Finance Bank' => t('Equitas Small Finance Bank'),
  					'Ujjivan Small Finance Bank' => t('Ujjivan Small Finance Bank'),
  					'Utkarsh Small Finance Bank' => t('Utkarsh Small Finance Bank'),
  					'Janalakshmi Small Finance Bank' => t('Janalakshmi Small Finance Bank'),
  					'Capital Lab Small Finance Bank' => t('Capital Lab Small Finance Bank'),
  					'Disha Small Finance Bank' => t('Disha Small Finance Bank'),
  					'ESAF Small Finance Bank' => t('ESAF Small Finance Bank'),
  					'RGVN Small Finance Bank' => t('RGVN Small Finance Bank'),
  					'Suryoday Small Finance Bank' => t('Suryoday Small Finance Bank'),
  					'Airtel Payments Bank Limited' => t('Airtel Payments Bank Limited'),
  					'India Post Payments Bank' => t('India Post Payments Bank'),
  					'Fino Payment Bank' => t('Fino Payment Bank'),
  					'Jio Payments Bank' => t('Jio Payments Bank'),
  					'Paytm Payments Bank' => t('Paytm Payments Bank')		
  			),
  			'#default_value' => $value->bank_name,
  			'#attributes' => array('disabled' => $disabled,),
  			'#states' => array(
  					'visible' => array(
  							':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
  					),
  					'required' => array(
  							':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
  					),
  			),
  	);
  }
  elseif($countryname_user=="Vietnam")
  {
  	$form['field_bank_name'] = array(
  			'#type' => 'select',
  			'#title' => t('Bank Name'),
  			'#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
  			'#suffix' => '</div>',
  			'#empty_option' => $this->t('-Please Select Bank-'),
  			'#options' => array(
  					'Orient Commercial Joint Stock Bank' => t('Orient Commercial Joint Stock Bank'),
  					'Asia Commercial Bank' => t('Asia Commercial Bank'),
  					'Tien Phong Bank' => t('Tien Phong Bank'),
  					'Hanoi Building Joint-stock Commercial Bank' => t('Hanoi Building Joint-stock Commercial Bank'),
  					'Maritime Commercial Joint Stock Bank' => t('Maritime Commercial Joint Stock Bank'),
  					'Sai Gon Thuong Tin Commercial Joint-stock Bank' => t('Sai Gon Thuong Tin Commercial Joint-stock Bank'),
  					'Eastern Asia Commercial Joint Stock Bank' => t('Eastern Asia Commercial Joint Stock Bank'),
  					'Viet nam Export' => t('Viet nam Export'),
  					'Nam A Commercial Joint Stock Bank' => t('Nam A Commercial Joint Stock Bank'),
  					'Saigon bank for Industry & Trade' => t('Saigon bank for Industry & Trade'),
  					'Vietnam Prosperity commercial joint-stock bank' => t('Vietnam Prosperity commercial joint-stock bank'),
  					'Viet Nam Technological and Commercial Joint Stock Bank' => t('Viet Nam Technological and Commercial Joint Stock Bank'),
  					'Military Commercial Joint Stock Bank' => t('Military Commercial Joint Stock Bank'),
  					'Bac A Commercial Joint Stock Bank' => t('Bac A Commercial Joint Stock Bank'),
  					'Vietnam International Commercial Joint Stock Bank' => t('Vietnam International Commercial Joint Stock Bank'),
  					'Southeast Asia Commercial Joint Stock Bank' => t('Southeast Asia Commercial Joint Stock Bank'),
  					'Housing development Commercial Joint Stock Bank' => t('Housing development Commercial Joint Stock Bank'),
  					'Southern Commercial Joint Stock Bank' => t('Southern Commercial Joint Stock Bank'),
  					'Viet Capital Commercial Joint Stock Bank' => t('Viet Capital Commercial Joint Stock Bank'),
  					'Sai Gon Joint Stock Commercial Bank' => t('Sai Gon Joint Stock Commercial Bank'),
  					'Viet A Commercial Joint Stock Bank' => t('Viet A Commercial Joint Stock Bank'),
  					'Saigon-Hanoi Commercial Joint Stock Bank' => t('Saigon-Hanoi Commercial Joint Stock Bank'),
  					'Global Petro Commercial Joint Stock Bank' => t('Global Petro Commercial Joint Stock Bank'),
  					'An Binh Commercial Joint Stock Bank' => t('An Binh Commercial Joint Stock Bank'),
  					'Nam Viet Commercial Joint Stock Bank' => t('Nam Viet Commercial Joint Stock Bank'),
  					'Kien Long Commercial Joint Stock Bank' => t('Kien Long Commercial Joint Stock Bank'),
  					'Mekong Commercial Bank' => t('Mekong Commercial Bank'),
  					'Viet Nam thuong Tin Commercial Joint Stock Bank' => t('Viet Nam thuong Tin Commercial Joint Stock Bank'),
  					'OCEAN Commercial Joint Stock Bank' => t('OCEAN Commercial Joint Stock Bank'),
  					'Petrolimex Group Commercial Joint Stock Bank' => t('Petrolimex Group Commercial Joint Stock Bank'),
  					'Western Rural Commercial Joint Stock Bank' => t('Western Rural Commercial Joint Stock Bank'),
  					'Great Trust Joint Stock Commercial Bank' => t('Great Trust Joint Stock Commercial Bank'),
  					'Great Asia Commercial Joint Stock Bank' => t('Great Asia Commercial Joint Stock Bank'),
  					'LienViet Post Commercial Joint Stock Bank' => t('LienViet Post Commercial Joint Stock Bank'),
  					'Mekong Development Joint Stock Commercial Bank' => t('Mekong Development Joint Stock Commercial Bank'),
  					'Bao Viet Joint Stock Commercial Bank' => t('Bao Viet Joint Stock Commercial Bank'),
  					'Western Bank (Phuong Tay Bank)' => t('Western Bank (Phuong Tay Bank)'),
  					'Viet Nam Public Bank (PVcomBank)' => t('Viet Nam Public Bank (PVcomBank)'),
  					'Tien Phong Joint Stock Commercial Bank (Tp Bank)' => t('Tien Phong Joint Stock Commercial Bank (Tp Bank)'),
  					'Standard Chartered Bank' => t('Standard Chartered Bank'),
  					'Australia and New Zealand Banking Group|ANZ' => t('Australia and New Zealand Banking Group|ANZ'),
  					'Citibank Vietnam' => t('Citibank Vietnam'),
  					'Bangkok Bank' => t('Bangkok Bank'),
  					'Bank of America|BOA' => t('Bank of America|BOA'),
  					'Natixis' => t('Natixis'),
  					'Deutsche Bank' => t('Deutsche Bank'),
  					'Mizuho Bank' => t('Mizuho Bank'),
  					'Home Finance Company|HFC Bank' => t('Home Finance Company|HFC Bank'),
  					'The Bank of Tokyo-Mitsubishi UF' => t('The Bank of Tokyo-Mitsubishi UF'),
  					'Commonwealth Bank of Australia' => t('Commonwealth Bank of Australia'),
  					'HSBC' => t('HSBC'),
  					'Shinhan Bank' => t('Shinhan Bank'),
  					'KEB Hana Bank' => t('KEB Hana Bank'),
  					'Industrial Bank of Korea' => t('Industrial Bank of Korea'),
  					'Scotiabank' => t('Scotiabank'),
  					'JP Morgan Chase Bank' => t('JP Morgan Chase Bank'),
  			
  			),
  			'#default_value' => $value->bank_name,
  			'#attributes' => array('disabled' => $disabled,),
  			'#states' => array(
  					'visible' => array(
  							':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
  					),
  					'required' => array(
  							':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
  					),
  			),
  	);
  }
  elseif($countryname_user=="Cambodia")
  {
  	$form['field_bank_name'] = array(
  			'#type' => 'select',
  			'#title' => t('Bank Name'),
  			'#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
  			'#suffix' => '</div>',
  			'#empty_option' => $this->t('-Please Select Bank-'),
  			'#options' => array(
  					'ACLEDA BANK Plc.' => t('ACLEDA BANK Plc.'),
  					'ADVANCED BANK OF ASIA LIMITED' => t('ADVANCED BANK OF ASIA LIMITED'),
  					'AGRIBANK CAMBODIA BRANCH' => t('AGRIBANK CAMBODIA BRANCH'),
  					'ANZ ROYAL BANK CAMBODIA' => t('ANZ ROYAL BANK CAMBODIA'),
  					'BANK FOR INVESTMENT& DEVELOPMENT OF CAMBODIA Plc.' => t('BANK FOR INVESTMENT& DEVELOPMENT OF CAMBODIA Plc.'),
  					'SBI Singapore' => t('SBI Singapore'),
  					'BANK OF CHINA LIMITED PHNOM PENH BRANCH' => t('BANK OF CHINA LIMITED PHNOM PENH BRANCH'),
  					'BANK OF INDIA Phnom Penh Branch' => t('BANK OF INDIA Phnom Penh Branch'),
  					'BOOYOUNG KHMER BANK' => t('BOOYOUNG KHMER BANK'),
  					'BRED BANK (CAMBODIA) PLC.' => t('BRED BANK (CAMBODIA) PLC.'),
  					'CAMBODIA ASIA BANK LTD.' => t('CAMBODIA ASIA BANK LTD.'),
  					'CAMBODIA MEKONG BANK PUBLIC LIMITED.' => t('CAMBODIA MEKONG BANK PUBLIC LIMITED.'),
  					'CAMBODIAN COMMERCIAL BANK LTD' => t('CAMBODIAN COMMERCIAL BANK LTD'),
  					'CAMBODIAN POST BANK Plc.' => t('CAMBODIAN POST BANK Plc.'),
  					'CAMBODIAN PUBLIC BANK Plc.,' => t('CAMBODIAN PUBLIC BANK Plc.,'),
  					'CANADIA BANK PLC.' => t('CANADIA BANK PLC.'),
  					'CIMB BANK PLC.' => t('CIMB BANK PLC.'),
  					'FIRST COMMERCIAL BANK PHNOM PENH BRANCH.' => t('FIRST COMMERCIAL BANK PHNOM PENH BRANCH.'),
  					'FOREIGN TRADE BANK OF CAMBODIA' => t('FOREIGN TRADE BANK OF CAMBODIA'),
  					'Hong Leong Bank (Cambodia) PLC' => t('Hong Leong Bank (Cambodia) PLC'),
  					'ICBC Bank Limited Phnom Penh Branch' => t('ICBC Bank Limited Phnom Penh Branch'),
  					'KOOKMIN BANK CAMBODIA.' => t('KOOKMIN BANK CAMBODIA.'),
  					'KRUNG THAI BANK PUBLIC CO' => t('KRUNG THAI BANK PUBLIC CO'),
  					'MAY BANK PHNOM PENH BRANCH.' => t('MAY BANK PHNOM PENH BRANCH.'),
  					'MB Bank Plc' => t('MB Bank Plc'),
  					'PHILLIP BANK PLC' => t('PHILLIP BANK PLC'),
  					'Mega International Commercial Bank Co.Ltd. Phnom Penh Branch' => t('Mega International Commercial Bank Co.Ltd. Phnom Penh Branch'),
  					'PHNOM PENH COMMERCIAL BANK' => t('PHNOM PENH COMMERCIAL BANK'),
  					'RHB Indochina Bank Limited' => t('RHB Indochina Bank Limited'),
  					'SACOM BANK Phnom Penh Branch' => t('SACOM BANK Phnom Penh Branch'),
  					'SATHAPANA BANK PLC' => t('SATHAPANA BANK PLC'),
  					'SHB Plc. Phnom Penh Branch Cambodia' => t('SHB Plc. Phnom Penh Branch Cambodia'),
  					'Taiwan Cooperative Bank, Phnom Penh Branch' => t('Taiwan Cooperative Bank, Phnom Penh Branch'),
  					'UNION COMMERCIAL BANK PLC.' => t('UNION COMMERCIAL BANK PLC.'),
  					'VATTANAC BANK' => t('VATTANAC BANK'),



  			),
  			'#default_value' => $value->bank_name,
  			'#attributes' => array('disabled' => $disabled,),
  			'#states' => array(
  					'visible' => array(
  							':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
  					),
  					'required' => array(
  							':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
  					),
  			),
  	);
  }
  else
  {
    $form['field_bank_name'] = array(
        '#type' => 'select',
        '#title' => t('Bank Name'),
        '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('-Please Select Bank-'),
        '#options' => array(
            'CIMB Bank Berhad' => t('CIMB Bank Berhad'),
            'Maybank (Malayan Banking Berhad)' => t('Maybank (Malayan Banking Berhad)'),
            'Citibank Berhad' => t('Citibank Berhad'),
            'Hong Leong Bank Berhad' => t('Hong Leong Bank Berhad'),
            'HSBC Bank Malaysia Berhad' => t('HSBC Bank Malaysia Berhad'),
            'OCBC Bank (Malaysia) Berhad' => t('OCBC Bank (Malaysia) Berhad'),
            'Public Bank Berhad' => t('Public Bank Berhad'),
            'RHB Bank Berhad' => t('RHB Bank Berhad'),
            'Standard Chartered Bank Malaysia Berhad' => t('Standard Chartered Bank Malaysia Berhad'),
            'Bank Rakyat' => t('Bank Rakyat'),
            'AmBank (M) Berhad' => t('AmBank (M) Berhad'),
            'Affin Bank Berhad' => t('Affin Bank Berhad'),
            'Alliance Bank Malaysia Berhad' => t('Alliance Bank Malaysia Berhad'),
            'AMMB Holdings' => t('AMMB Holdings'),
            'Bangkok Bank Berhad' => t('Bangkok Bank Berhad'),
            'Bank of America Malaysia Berhad' => t('Bank of America Malaysia Berhad'),
            'Bank of China (Malaysia) Berhad' => t('Bank of China (Malaysia) Berhad'),
            'Bank of Tokyo-Mitsubishi UFJ (Malaysia) Berhad' => t('Bank of Tokyo-Mitsubishi UFJ (Malaysia) Berhad'),
            'BNP Paribas Malaysia Berhad' => t('BNP Paribas Malaysia Berhad'),
            'China Construction Bank (Malaysia) Berhad' => t('China Construction Bank (Malaysia) Berhad'),
            'Deutsche Bank (Malaysia) Berhad' => t('Deutsche Bank (Malaysia) Berhad'),
            'India International Bank (Malaysia) Berhad' => t('India International Bank (Malaysia) Berhad'),
            'Industrial and Commercial Bank of China (Malaysia) Berhad' => t('Industrial and Commercial Bank of China (Malaysia) Berhad'),
            'J.P. Morgan Chase Bank Berhad' => t('J.P. Morgan Chase Bank Berhad'),
            'Mizuho Bank (Malaysia) Berhad' => t('Mizuho Bank (Malaysia) Berhad'),
            'National Bank of Abu Dhabi Malaysia Berhad' => t('National Bank of Abu Dhabi Malaysia Berhad'),
            'Sumitomo Mitsui Banking Corporation Malaysia Berhad' => t('Sumitomo Mitsui Banking Corporation Malaysia Berhad'),
            'The Bank of Nova Scotia Berhad' => t('The Bank of Nova Scotia Berhad'),
            'The Royal Bank of Scotland Berhad' => t('The Royal Bank of Scotland Berhad'),
            'United Overseas Bank (Malaysia) Bhd.' => t('United Overseas Bank (Malaysia) Bhd.'),
            'EON Bank Berhad' => t('EON Bank Berhad'),
            'Affin Islamic Bank Berhad' => t('Affin Islamic Bank Berhad'),
            'Al Rajhi Banking & Investment Corporation (Malaysia) Berhad' => t('Al Rajhi Banking & Investment Corporation (Malaysia) Berhad'),
            'Alliance Islamic Bank Berhad' => t('Alliance Islamic Bank Berhad'),
            'AmIslamic Bank Berhad' => t('AmIslamic Bank Berhad'),
            'Asian Finance Bank Berhad' => t('Asian Finance Bank Berhad'),
            'Bank Islam Malaysia Berhad' => t('Bank Islam Malaysia Berhad'),
            'Bank Muamalat Malaysia Berhad' => t('Bank Muamalat Malaysia Berhad'),
            'CIMB Islamic Bank Berhad' => t('CIMB Islamic Bank Berhad'),
            'HSBC Amanah Malaysia Berhad' => t('HSBC Amanah Malaysia Berhad'),
            'Hong Leong Islamic Bank Berhad' => t('Hong Leong Islamic Bank Berhad'),
            'Kuwait Finance House (Malaysia) Berhad' => t('Kuwait Finance House (Malaysia) Berhad'),
            'Maybank Islamic Berhad' => t('Maybank Islamic Berhad'),
            'OCBC Al-Amin Bank Berhad' => t('OCBC Al-Amin Bank Berhad'),
            'Public Islamic Bank Berhad' => t('Public Islamic Bank Berhad'),
            'RHB Islamic Bank Berhad' => t('RHB Islamic Bank Berhad'),
            'Standard Chartered Saadiq Berhad' => t('Standard Chartered Saadiq Berhad')
            
        ),
        '#default_value' => $value->bank_name,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
         ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
    
  }
    $form['field_bank_code'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank Code'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>', 
      '#default_value' =>$value->bank_code,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    $form['field_bank_address'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank Address'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>', 
      '#default_value' =>$value->bank_address,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    $form['field_branch_code'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank Branch Code'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>', 
      '#default_value' =>$value->bank_branch_code,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    $form['field_bank_branch'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank Branch'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>', 
      '#default_value' =>$value->bank_branch,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    $form['field_swift_code'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank SWIFT-Code'),
      '#default_value' =>$value->bank_swift_code,
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>', 
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    $form['field_account_number'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank Account Number'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>', 
      '#default_value' =>$value->account_number,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    
    $form['field_account_first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Account Holder First Name'),
      '#default_value' =>$value->account_first_name,
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>', 
      '#attributes' => array('disabled' => $disabled,),'#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    
    $form['field_account_last_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Account Holder Last Name'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>', 
      '#default_value' =>$value->account_last_name,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    
    $form['field_account_holder_city'] = array(
      '#type' => 'textfield',
      '#title' => t('Account Holder City'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>', 
      '#default_value' =>$value->account_holder_city,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    
    $form['field_account_holder_address'] = array(
      '#type' => 'textfield',
      '#title' => t('Account Holder Address'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>', 
      '#default_value' =>$value->account_holder_address,
      '#attributes' => array('disabled' => $disabled,),
      '#states' => array(
        'visible' => array(
          ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
         ),
         'required' => array(
      ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
    ),
      ),
    );
    
    if($value->account_holder_country_code=="" && $uservalue->country_code!="")
    {
      $form['field_account_holder_country_code'] = array(
        '#type' => 'select',
        '#title' => t('Country Code'),
        '#prefix' => '<div class="col-md-3">',
        '#suffix' => '</div>',
        '#empty_option' => $this->t('Select'),
        '#options' => $phone,
        '#default_value' =>$uservalue->country_code,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
      );
    }
    elseif($value->account_holder_country_code=="" && $uservalue->country_code=="" && $country_user!="")
    {
      
          $query1 = db_select('custom_country','cc');
        $query1->fields('cc');
        $query1->condition('id', $country_user);
        $result1 = $query1->execute();
        $data1 = $result1->fetchObject();
        $country_code=$data1->phone_code;
        $form['field_account_holder_country_code'] = array(
            '#type' => 'select',
            '#title' => t('Country Code'),
            '#prefix' => '<div class="col-md-3">',
            '#suffix' => '</div>',
            '#empty_option' => $this->t('Select'),
            '#options' => $phone,
            '#default_value' =>$country_code,
            '#attributes' => array('disabled' => $disabled,),
            '#states' => array(
            'visible' => array(
              ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
             ),
             'required' => array(
              ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
            ),
          ),
        );
      
    }
    else
    {
      $form['field_account_holder_country_code'] = array(
          '#type' => 'select',
          '#title' => t('Country Code'),
          '#prefix' => '<div class="col-md-3">',
          '#suffix' => '</div>',
          '#empty_option' => $this->t('Select'),
          '#options' => $phone,
          '#default_value' =>$value->account_holder_country_code,
          '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
          'visible' => array(
            ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
           ),
           'required' => array(
            ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
          ),
         ),
      );
    }
    if($value->account_holder_phone=="" && $uservalue->phone_number!="")
    {
      $form['field_account_holder_phone'] = array(
        '#type' => 'textfield',
        '#title' => t('Account Holder Phone number'),
        '#prefix' => '<div class="col-md-3">',
        '#suffix' => '</div></div>', 
        '#default_value' =>$uservalue->phone_number,
        '#attributes' => array('disabled' => $disabled,),
        '#states' => array(
      'visible' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
       ),
       'required' => array(
        ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
      ),
      ),
    );
    }
    else 
    {
      $form['field_account_holder_phone'] = array(
          '#type' => 'textfield',
          '#title' => t('Account Holder Phone number'),
          '#prefix' => '<div class="col-md-3">',
          '#suffix' => '</div></div>',
          '#default_value' =>$value->account_holder_phone,
          '#attributes' => array('disabled' => $disabled,),
          '#states' => array(
          'visible' => array(
            ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
           ),
           'required' => array(
            ':input[name="field_payment_methods"]' => array('value' => 'bank_transfer'),
          ),
          ),
      );
    }
    $query = db_select('custom_users','c');
    $query->fields('c');
    $query->condition('id', $user_uid);
    $result = $query->execute();
    $data = $result->fetchObject();
    $role=$data->role;
    $form['markup_mandatory'] = array('#markup' => "<p class='text-danger'> * denotes mandatory fields</p>");
    $form['back'] = array(
        '#type' => 'markup',
        '#markup' => '<span onClick="history.go(-1); return false;" class="btn btn-default w-150 m-sm-r m-md-b back-button">Back</span>',
    );
    $user_role = \Drupal::currentUser()->getRoles();
    if($user_role[1]=="diplomats" || $user_role[1]=="promoters")
    {
      //if($value->refer_management_approval=="0")
      //{
      $form['next'] = array(
          '#type' => 'submit',
          '#value' => t('Submit Profile'),
          '#submit' => array('::newSubmissionHandlerNext'),
          '#attributes' => array("class"=>array(" btn btn-primary w-150 m-sm-r m-md-b")),
      );
      //}
    }
    else
    {
      $form['next'] = array(
          '#type' => 'submit',
          '#value' => t('Save'),
          '#submit' => array('::newSubmissionHandlerNext'),
          '#attributes' => array("class"=>array(" btn btn-primary w-150 m-sm-r m-md-b")),
      );
          
    
  }
   
    
    /*$form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r"),
                      'disabled' => $disabled,),
    );*/

   
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $payment_methods = $form_state->getValue('field_payment_methods');
    if($payment_methods == 'bank_transfer'){
      $account_number = $form_state->getValue('field_account_number');
      $account_holder_phone_number = $form_state->getValue('field_account_holder_phone');
      $selected_country = $form_state->getValue('field_account_holder_country_code');
      $numberlength = strlen($account_holder_phone_number);
     if (!is_numeric($account_number))
      {
        $form_state->setErrorByName('field_account_number', t('That is not a valid account number.'));
      }
    if (!is_numeric($account_holder_phone_number))
      {
        $form_state->setErrorByName('field_account_holder_phone', t('That is not a valid phone number.'));
      }
      
    /*Malasiya*/
     if($selected_country == '+60'){
       if(($numberlength < 8) || ($numberlength > 11)){
         $form_state->setErrorByName('field_account_holder_phone', t('Please enter valid phone number(Minimum digit: 8 or maximum digit: 11).'));
       }
     }
     
     /*Indonesia*/
     if($selected_country == '+62'){
       if(($numberlength < 9) || ($numberlength > 12)){
         $form_state->setErrorByName('field_account_holder_phone', t('Please enter valid phone number(Minimum digit: 9 or maximum digit: 12).'));
       }
     }
     
     /*Singapore*/
     if($selected_country == '+65'){
       if(($numberlength < 9) || ($numberlength > 11)){
         $form_state->setErrorByName('field_account_holder_phone', t('Please enter valid phone number(Minimum digit: 9 or maximum digit: 11).'));
       }
     }
     
     /*Thailand*/
     if($selected_country == '+66'){
       if(($numberlength < 8) || ($numberlength > 11)){
         $form_state->setErrorByName('field_account_holder_phone', t('Please enter valid phone number(Minimum digit: 8 or maximum digit: 11).'));
       }
     }
     
     /* Philippines*/
     if($selected_country == '+63'){
      if(($numberlength < 9) || ($numberlength > 12)){
         $form_state->setErrorByName('field_account_holder_phone', t('Please enter valid phone number(Minimum digit: 8 or maximum digit: 12).'));
       }
     }
   }
   if($payment_methods == 'paypal'){
    $paypal_email = $form_state->getValue('field_paypal_emailid');
    $output = valid_email_address($paypal_email);
    if($output == FALSE){
      $form_state->setErrorByName('field_paypal_emailid', t('Please enter valid email address')); 
    }
    //if (!valid_email_address($paypal_email)) {
    //  $form_state->setErrorByName('field_paypal_emailid', t('Please enter valid email address'));
    //}
    
   }
   
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $user_payment_uid = \Drupal::currentUser()->id();
    $payment_method = $form_state->getValue('field_payment_methods');
    $paypal_emailid = $form_state->getValue('field_paypal_emailid');
    $bank_name = $form_state->getValue('field_bank_name');
    $bank_code = $form_state->getValue('field_bank_code');
    $bank_address = $form_state->getValue('field_bank_address');
    $branch_code = $form_state->getValue('field_branch_code');
    $branch = $form_state->getValue('field_bank_branch');
    $swift_code = $form_state->getValue('field_swift_code');
    $account_number = $form_state->getValue('field_account_number');
    $account_first_name = $form_state->getValue('field_account_first_name');
    $account_last_name = $form_state->getValue('field_account_last_name');
    $account_holder_city = $form_state->getValue('field_account_holder_city');
    $account_holder_address = $form_state->getValue('account_holder_address');
    $account_holder_phone_number = $form_state->getValue('field_account_holder_phone');
    $account_holder_country_code = $form_state->getValue('field_account_country_code');
    
    
    $inputarray = array("uid"=>$user_payment_uid,"payment_method"=>$payment_method,"paypal_emailid"=>$paypal_emailid,"bank_name"=>$bank_name,"bank_code"=>$bank_code,"bank_address"=>$bank_address,"branch_code"=>$branch_code,"branch"=>$branch,"swift_code"=>$swift_code,"account_number"=>$account_number,"account_first_name"=>$account_first_name,"account_last_name"=>$account_last_name,"account_holder_city"=>$account_holder_city,"account_holder_address"=>$account_holder_address,"account_holder_phone_number"=>$account_holder_phone_number);

      $classobj = new APIController();

      $result = $classobj->payment_details_update($inputarray);

      if($result == 1){

          drupal_set_message('Your Payment Details Completed Successfully.');

      } else {

         drupal_set_message($result);
      }
  }
   
    /**
   * Custom submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function newSubmissionHandlerNext(array &$form, FormStateInterface $form_state) {
      global $base_url;
      $user_payment_uid = \Drupal::currentUser()->id();
      $payment_method = $form_state->getValue('field_payment_methods');
      $paypal_emailid = $form_state->getValue('field_paypal_emailid');
    $bank_name = $form_state->getValue('field_bank_name');
    $bank_code = $form_state->getValue('field_bank_code');
    $bank_address = $form_state->getValue('field_bank_address');
    $branch_code = $form_state->getValue('field_branch_code');
    $branch = $form_state->getValue('field_bank_branch');
    $swift_code = $form_state->getValue('field_swift_code');
    $account_number = $form_state->getValue('field_account_number');
    $account_first_name = $form_state->getValue('field_account_first_name');
    $account_last_name = $form_state->getValue('field_account_last_name');
    $account_holder_city = $form_state->getValue('field_account_holder_city');
    $account_holder_address = $form_state->getValue('field_account_holder_address');
    $account_holder_phone_number = $form_state->getValue('field_account_holder_phone');
    $account_holder_country_code = $form_state->getValue('field_account_holder_country_code');
    $inputarray = array("uid"=>$user_payment_uid,"payment_method"=>$payment_method,"paypal_emailid"=>$paypal_emailid,"bank_name"=>$bank_name,"bank_code"=>$bank_code,"bank_address"=>$bank_address,"branch_code"=>$branch_code,"branch"=>$branch,"swift_code"=>$swift_code,"account_number"=>$account_number,"account_first_name"=>$account_first_name,"account_last_name"=>$account_last_name,"account_holder_city"=>$account_holder_city,"account_holder_address"=>$account_holder_address,"account_holder_phone_number"=>$account_holder_phone_number,"account_holder_country_code"=>$account_holder_country_code);

      $classobj = new APIController();
      
      $result = $classobj->payment_details_update($inputarray);
      if($result=="1")
      {
        //Update location contact status to 1 in custom users information table
        $query = \Drupal::database()->update('custom_users_information');
        $query->fields(['payment_status' => 1]);
        $query->condition('id', $user_payment_uid);
        $query->execute();
      }
      // set relative internal path
      $query = db_select('custom_users','c');
      $query->fields('c');
      $query->condition('id', $user_payment_uid);
      $result = $query->execute();
      $data = $result->fetchObject();
      $role=$data->role;
      $mail=$data->email;
      $query1 = db_select('custom_users_information','cu');
      $query1->fields('cu');
      $query1->condition('id', $user_payment_uid);
      $result1 = $query1->execute();
      $data1 = $result1->fetchObject();
      
      $introduce_status=$data1->introduce_status;
      $contact_status=$data1->contact_status;
      $location_status=$data1->location_status;
      $payment_status=$data1->payment_status;
      $refer_status=$data1->refer_management_approval;
      $first_name=$data1->first_name;
      $last_name=$data1->last_name;
      $full_name=ucfirst($first_name)." ".ucfirst($last_name);
      //check if all status complete
      $msg="";
      $user_role = \Drupal::currentUser()->getRoles();
      if($user_role[1]=="local_experts")
      {
        if($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" )
        {
          
          $redirect_path_next = "/profile/thank-you-localexpert";
          $url_next = url::fromUserInput($redirect_path_next);
          // set redirect
          $form_state->setRedirectUrl($url_next);
        }
        else
        {
          if($introduce_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Introduce Yourself Section";
            }
            
          }
          if($contact_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Contact Details";
            }
            else
            {
              $msg=$msg. ", Contact Details ";
            }
            
          }
          if($location_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Location Details";
            }
            else
            {
              $msg=$msg. ", Location Details ";
            }
            
          }
          if($payment_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Payment Details";
            }
            else
            {
              $msg=$msg. "& Payment Details ";
            }
            
            
          }
          if($msg!='')
          {
            drupal_set_message($msg. " To complete your profile." ,'warning');
          }
          else
          {
            
            drupal_set_message("Profile details saved successfully." ,'success');
            
          }
          //drupal_set_message($msg. " To complete your profile." ,'warning');
        }
      }
      if($user_role[1]=="diplomats" || $user_role[1]=="partners")
      {
        if($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="1")
        {
          
          $redirect_path_next = "/profile/refer-earn";
          $url_next = url::fromUserInput($redirect_path_next);
          // set redirect
          $form_state->setRedirectUrl($url_next);
        }
        if($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="0")
        {
        	if($user_role[1]=="diplomats")
          {
            $redirect_path_next = "/profile/thank-you-diplomat";
            
          }
          elseif($user_role[1]=="partners")
          {
            $redirect_path_next = "/profile/thank-you-promoter";
          }
          $url_next = url::fromUserInput($redirect_path_next);
          // set redirect
          $form_state->setRedirectUrl($url_next);
          //$msg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:880px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($first_name).' '.ucfirst($last_name).",<br /><br />Your password change is successful. <br> Please log in to <a href='$base_url/user/login' >your account </a> with the new password.<br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
          
          $msg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:770px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($first_name).' '.ucfirst($last_name).",<br /><br />Thank you for taking the time to submit your profile !  <br> We have received your application and are in the midst of reviewing your profile. Our team will be in touch with you in a jiffy should we find your profile suitable.<br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
          $input = array("from"=>"Ezplor <welcome@ezplor.com>","to"=>$mail,"subject"=>"Thanks for submitting your profile!","category"=>"Registration","msg"=>$msg);
          $mail_out = APIController::global_mail_trigger($input);
          // Email to all editors and management a diplomat/partners submit profile  //
          $query = \Drupal::database()->select('custom_users', 'c');
          $query->fields('c', ['email', 'id','role','status' ]);
          $query->condition(
              db_or()
              ->condition('c.role', 'editors')
              ->condition('c.role','management')
              );
          $query->condition('c.status', '1');
          $result = $query->execute();
          $data = $result->fetchAll();
          
          foreach ($data as $value) {
            $dmail=$value->email;
            $did=$value->id;
            $squery = db_select('custom_users_information','cu');
            $squery->fields('cu');
            $squery->condition('user_id',$did,'=');
            $sresult = $squery->execute();
            $sdata = $sresult->fetchObject();
            $dfirst_name=$sdata->first_name;
            $dlast_name=$sdata->last_name;
            $drole=$value->role;
            if($user_role[1]=="diplomats")
            {
              if($drole=="management")
              {
                $dmsg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:800px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($dfirst_name).' '.ucfirst($dlast_name).",<br /><br />We are notifying you that a Diplomat has just submitted their profile. Here is the details : <br> Full Name : $full_name <br> Email Id : $mail <br> You may view the profile and verify <a href='$base_url/dashboard/profile-management'>here</a><br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
              }
              elseif($drole=="editors")
              {
                $dmsg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:800px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($dfirst_name).' '.ucfirst($dlast_name).",<br /><br />We are notifying you that a Diplomat has just submitted their profile. Here is the details : <br> Full Name : $full_name <br> Email Id : $mail <br> You may view the profile and verify <a href='$base_url/dashboard/profile-editor'>here</a><br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
                
              }
              $dsub="A Diplomat just submitted his profile!";
            
            }
            elseif ($user_role[1]=="partners")
            {
              if($drole=="management")
              {
                $dmsg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:800px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($dfirst_name).' '.ucfirst($dlast_name).",<br /><br />We are notifying you that a Promoter has just submitted their profile. Here is the details : <br> Full Name : $full_name <br> Email Id : $mail <br> You may view the profile and verify <a href='$base_url/dashboard/profile-management'>here</a><br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
              }
              elseif($drole=="editors")
              {
                $dmsg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:800px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Dear ".ucfirst($dfirst_name).' '.ucfirst($dlast_name).",<br /><br />We are notifying you that a Promoter has just submitted their profile. Here is the details : <br> Full Name : $full_name <br> Email Id : $mail <br> You may view the profile and verify <a href='$base_url/dashboard/profile-editor'>here</a><br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
                
              }
              $dsub="A Promoter just submitted his profile!";
            }
            
            
            $dinput = array("from"=>"Ezplor <welcome@ezplor.com>","to"=>$dmail,"subject"=>$dsub,"category"=>"Registration","msg"=>$dmsg);
            
            $dmail_out = APIController::global_mail_trigger($dinput);
            
          }
        }
      
        elseif($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="3")
        {
          drupal_set_message($msg. "Profile Details Updated.But Your refer and earn access is deactivated.Please contact Ezplor Team." ,'error');
        }
        elseif($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="2")
        {
          drupal_set_message($msg. "Profile Details Updated. But Your Profile is rejected by Ezplor Team.So you cannot access Refer & Earn" ,'error');
        } 
        else
        {
          if($introduce_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Introduce Yourself Section";
            }
            
          }
          if($contact_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Contact Details";
            }
            else
            {
              $msg=$msg. ", Contact Details ";
            }
            
          }
          if($location_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Location Details";
            }
            else
            {
              $msg=$msg. ", Location Details ";
            }
            
          }
          if($payment_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Payment Details";
            }
            else
            {
              $msg=$msg. "& Payment Details ";
            }
            
            
          }
          drupal_set_message($msg. " To complete your profile." ,'warning');
        }
      }
      elseif($user_role[1]=="guests")
        {
          if($introduce_status=="1" && $contact_status=="1" && $location_status=="1"  )
          {
            
            $redirect_path_next = "/profile/thank-you";
            $url_next = url::fromUserInput($redirect_path_next);
            // set redirect
            $form_state->setRedirectUrl($url_next);
          }
          else
          {
            if($introduce_status=="0" )
            {
              if($msg=="")
              {
                $msg=$msg. "Please fill in Introduce Yourself Section";
              }
              
            }
            if($contact_status=="0" )
            {
              if($msg=="")
              {
                $msg=$msg. "Please fill in Contact Details";
              }
              else
              {
                $msg=$msg. ", Contact Details ";
              }
              
            }
            if($location_status=="0" )
            {
              if($msg=="")
              {
                $msg=$msg. "Please fill in Location Details";
              }
              else
              {
                $msg=$msg. "& Location Details ";
              }
              
            }
            
            if($msg!='')
            {
              drupal_set_message($msg. " To complete your profile." ,'warning');
            }
            else
            {
              
              drupal_set_message("Profile details saved successfully." ,'success');
              
            }
          }
      }
      
      /*else
      {
        if($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" )
        {
          
          $redirect_path_next = "/profile/thank-you";
          $url_next = url::fromUserInput($redirect_path_next);
          // set redirect
          $form_state->setRedirectUrl($url_next);
        }
        else
        {
          if($introduce_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Introduce Yourself Section";
            }
            
          }
          if($contact_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Contact Details";
            }
            else
            {
              $msg=$msg. ", Contact Details ";
            }
            
          }
          if($location_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Location Details";
            }
            else
            {
              $msg=$msg. ", Location Details ";
            }
            
          }
          if($payment_status=="0" )
          {
            if($msg=="")
            {
              $msg=$msg. "Please fill in Payment Details";
            }
            else
            {
              $msg=$msg. "& Payment Details ";
            }
            
            
          }
          drupal_set_message($msg. " To complete your profile." ,'warning');
        }
      }*/
   
  }
}
?>
