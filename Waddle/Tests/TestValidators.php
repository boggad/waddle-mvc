<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 21.11.15
 * Time: 19:06
 */

namespace Engine\Tests;

use Waddle\Classes\Validators\Required;

class TestValidators extends \PHPUnit_Framework_TestCase {

    public function testRequiredValidator() {
        $validator = new Required('The field is required!');
        $this->assertTrue($validator->validate('Data'), 'Required: String value is required');
        $this->assertTrue($validator->validate('') === false, 'Required: Empty string');
        $this->assertTrue($validator->validate(10), 'Required: integer value');
        $this->assertTrue($validator->validate(0), 'Required: zero integer, can be zero');
        $this->assertTrue((new Required('The field is required!', false))->validate(0), 'Required: can\'t be zero');
        $this->assertTrue($validator->validate(null) === false, 'Required: null value');
    }
}