<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Cx\Core_Modules\License;
/**
 * Description of Person
 *
 * @author Michael Ritter <michael.ritter@comvation.com>
 */
class Person {
    private $companyName;
    private $title;
    private $firstname;
    private $lastname;
    private $address;
    private $zip;
    private $city;
    private $country;
    private $phone;
    private $url;
    private $mail;
    
    public function __construct($companyName = '', $title = '', $firstname = '', $lastname = '', $address = '', $zip = '', $city = '', $country = '', $phone = '', $url = '', $mail = '') {
        $this->companyName = $companyName;
        $this->title = $title;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->address = $address;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->phone = $phone;
        $this->url = $url;
        $this->mail = $mail;
    }
    
    public function getCompanyName() {
        return $this->companyName;
    }
    
    public function setCompanyName($companyName) {
        $this->companyName = $companyName;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getFirstname() {
        return $this->firstname;
    }
    
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }
    
    public function getLastname() {
        return $this->lastname;
    }
    
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setAddress($address) {
        $this->address = $address;
    }
    
    public function getZip() {
        return $this->zip;
    }
    
    public function setZip($zip) {
        $this->zip = $zip;
    }
    
    public function getCity() {
        return $this->city;
    }
    
    public function setCity($city) {
        $this->city = $city;
    }
    
    public function getCountry() {
        return $this->country;
    }
    
    public function setCountry($country) {
        $this->country = $country;
    }
    
    public function getPhone() {
        return $this->phone;
    }
    
    public function setPhone($phone) {
        $this->phone = $phone;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function setUrl($url) {
        $this->url = $url;
    }
    
    public function getMail() {
        return $this->mail;
    }
    
    public function setMail($mail) {
        $this->mail = $mail;
    }
}
