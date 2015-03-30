<?php

/**
* Map Connec Customer Person representation to/from vTiger Contact
*/
class ContactMapper extends BaseMapper {
  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'Person';
    $this->local_entity_name = 'Contacts';
    $this->connec_resource_name = 'people';
    $this->connec_resource_endpoint = 'people';
  }

  // Return the Person local id
  protected function getId($person) {
    return $person->id;
  }

  // Return a local Person by id
  protected function loadModelById($local_id) {
    $person = CRMEntity::getInstance("Contacts");
    $person->retrieve_entity_info($local_id, "Contacts");
    vtlib_setup_modulevars("Contacts", $person);
    $person->id = $local_id;
    $person->mode = 'edit';
    return $person;
  }

  protected function validate($resource_hash) {
    // Process only Customers
    return $resource_hash['is_customer'];
  }

  // Map the Connec resource attributes onto the vTiger Person
  protected function mapConnecResourceToModel($person_hash, $person) {
    // Map hash attributes to Person
    if($this->is_set($person_hash['code'])) { $person->column_fields['contact_no'] = $person_hash['code']; }
    if($this->is_set($person_hash['title'])) { $person->column_fields['salutation'] = $person_hash['title']; }
    if($this->is_set($person_hash['first_name'])) { $person->column_fields['firstname'] = $person_hash['first_name']; }
    if($this->is_set($person_hash['last_name'])) { $person->column_fields['lastname'] = $person_hash['last_name']; }
    if($this->is_set($person_hash['description'])) { $person->column_fields['description'] = $person_hash['description']; }

    if($this->is_set($person_hash['address_work'])) {
      if($this->is_set($person_hash['address_work']['billing'])) {
        $billing_address = $person_hash['address_work']['billing'];
        if($this->is_set($billing_address['line1'])) { $person->column_fields['otherstreet'] = $billing_address['line1']; }
        if($this->is_set($billing_address['line2'])) { $person->column_fields['otherpobox'] = $billing_address['line2']; }
        if($this->is_set($billing_address['city'])) { $person->column_fields['othercity'] = $billing_address['city']; }
        if($this->is_set($billing_address['region'])) { $person->column_fields['otherstate'] = $billing_address['region']; }
        if($this->is_set($billing_address['postal_code'])) { $person->column_fields['otherzip'] = $billing_address['postal_code']; }
        if($this->is_set($billing_address['country'])) { $person->column_fields['othercountry'] = $billing_address['country']; }
      }

      if($this->is_set($person_hash['address_work']['shipping'])) {
        $shipping_address = $person_hash['address_work']['shipping'];
        if($this->is_set($shipping_address['line1'])) { $person->column_fields['mailingstreet'] = $shipping_address['line1']; }
        if($this->is_set($shipping_address['line2'])) { $person->column_fields['mailingpobox'] = $shipping_address['line2']; }
        if($this->is_set($shipping_address['city'])) { $person->column_fields['mailingcity'] = $shipping_address['city']; }
        if($this->is_set($shipping_address['region'])) { $person->column_fields['mailingstate'] = $shipping_address['region']; }
        if($this->is_set($shipping_address['postal_code'])) { $person->column_fields['mailingzip'] = $shipping_address['postal_code']; }
        if($this->is_set($shipping_address['country'])) { $person->column_fields['mailingcountry'] = $shipping_address['country']; }
      }
    }

    if($this->is_set($person_hash['phone_work'])) {
      if($this->is_set($person_hash['phone_work']['landline'])) { $person->column_fields['phone'] = $person_hash['phone_work']['landline']; }
      if($this->is_set($person_hash['phone_work']['landline2'])) { $person->column_fields['otherphone'] = $person_hash['phone_work']['landline2']; }
    }

    if($this->is_set($person_hash['phone_home']) && $this->is_set($person_hash['phone_home']['landline'])) {
      $person->column_fields['homephone'] = $person_hash['phone_home']['landline'];
    }

    if($this->is_set($person_hash['email']['address'])) { $person->column_fields['email'] = $person_hash['email']['address']; }
    if($this->is_set($person_hash['email']['address2'])) { $person->column_fields['secondaryemail'] = $person_hash['email']['address2']; }
  }

  // Map the vTiger Person to a Connec resource hash
  protected function mapModelToConnecResource($person) {
    $person_hash = array();

    // Save as Customer
    $person_hash['is_customer'] = true;

    // Map attributes
    if($this->is_set($person->column_fields['contact_no'])) { $person_hash['code'] = $person->column_fields['contact_no']; }
    if($this->is_set($person->column_fields['salutation'])) { $person_hash['title'] = $person->column_fields['salutation']; }
    if($this->is_set($person->column_fields['firstname'])) { $person_hash['first_name'] = $person->column_fields['firstname']; }
    if($this->is_set($person->column_fields['lastname'])) { $person_hash['last_name'] = $person->column_fields['lastname']; }
    if($this->is_set($person->column_fields['description'])) { $person_hash['description'] = $person->column_fields['description']; }
    
    $address = array();
    $billing_address = array();
    if($this->is_set($person->column_fields['otherstreet'])) { $billing_address['line1'] = $person->column_fields['otherstreet']; }
    if($this->is_set($person->column_fields['otherpobox'])) { $billing_address['line2'] = $person->column_fields['otherpobox']; }
    if($this->is_set($person->column_fields['othercity'])) { $billing_address['city'] = $person->column_fields['othercity']; }
    if($this->is_set($person->column_fields['otherstate'])) { $billing_address['region'] = $person->column_fields['otherstate']; }
    if($this->is_set($person->column_fields['otherzip'])) { $billing_address['postal_code'] = $person->column_fields['otherzip']; }
    if($this->is_set($person->column_fields['othercountry'])) { $billing_address['country'] = $person->column_fields['othercountry']; }
    if(!empty($billing_address)) { $address['billing'] = $billing_address; }

    $shipping_address = array();
    if($this->is_set($person->column_fields['mailingstreet'])) { $shipping_address['line1'] = $person->column_fields['mailingstreet']; }
    if($this->is_set($person->column_fields['mailingpobox'])) { $shipping_address['line2'] = $person->column_fields['mailingpobox']; }
    if($this->is_set($person->column_fields['mailingcity'])) { $shipping_address['city'] = $person->column_fields['mailingcity']; }
    if($this->is_set($person->column_fields['mailingstate'])) { $shipping_address['region'] = $person->column_fields['mailingstate']; }
    if($this->is_set($person->column_fields['mailingzip'])) { $shipping_address['postal_code'] = $person->column_fields['mailingzip']; }
    if($this->is_set($person->column_fields['mailingcountry'])) { $shipping_address['country'] = $person->column_fields['mailingcountry']; }
    if(!empty($shipping_address)) { $address['shipping'] = $shipping_address; }
    if(!empty($address)) { $person_hash['address_work'] = $address; }

    $phone_work_hash = array();
    if($this->is_set($person->column_fields['phone'])) { $phone_work_hash['landline'] = $person->column_fields['phone']; }
    if($this->is_set($person->column_fields['otherphone'])) { $phone_work_hash['landline2'] = $person->column_fields['otherphone']; }
    if(!empty($phone_work_hash)) { $person_hash['phone_work'] = $phone_work_hash; }

    if($this->is_set($person->column_fields['homephone'])) {$person_hash['phone_home'] = array('landline' => $person->column_fields['homephone']); }

    $email_hash = array();
    if($this->is_set($person->column_fields['email'])) { $email_hash['address'] = $person->column_fields['email']; }
    if($this->is_set($person->column_fields['secondaryemail'])) { $email_hash['address2'] = $person->column_fields['secondaryemail']; }
    if(!empty($email_hash)) { $person_hash['email'] = $email_hash; }

    return $person_hash;
  }

  // Persist the vTiger Person
  protected function persistLocalModel($person, $resource_hash) {
    $person->save("Contacts", $person->id, false);
  }
}
