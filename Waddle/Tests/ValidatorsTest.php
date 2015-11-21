<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 21.11.15
 * Time: 19:06
 */

namespace Waddle\Tests;

use Waddle\Classes\Validators\Email;
use Waddle\Classes\Validators\NumberBetween;
use Waddle\Classes\Validators\Required;

class ValidatorsTest extends \PHPUnit_Framework_TestCase {

    public function testRequiredValidator() {
        $validator = new Required('The field is required!');
        $this->assertEquals('The field is required!', $validator->getError());
        $this->assertTrue($validator->validate('Data'), 'Required: String value is required');
        $this->assertFalse($validator->validate(''), 'Required: Empty string');
        $this->assertTrue($validator->validate(10), 'Required: integer value');
        $this->assertTrue($validator->validate(0), 'Required: zero integer, can be zero');
        $this->assertFalse((new Required('The field is required!', false))->validate(0), 'Required: can\'t be zero');
        $this->assertFalse($validator->validate(null), 'Required: null value');
    }

    public function testEmailValidator() {
        $validator = new Email('Enter correct email!');
        $this->assertEquals('Enter correct email!', $validator->getError());
        $this->assertTrue($validator->validate('test@gmail.com'), 'Simplest email');
        $this->assertTrue($validator->validate('test-555.dot@what-a-domain.com'), 'Email with dashes and dots');
        $this->assertTrue($validator->validate('sometag+test-555.dot@what-a-domain.com'), 'Email with tag');
        $this->assertTrue($validator->validate('username@domain.longtimeago'),
            'Email with long top domain name');
        $this->assertFalse($validator->validate('not valid@email.com'), 'Spaces in email');
        $this->assertFalse($validator->validate('valid@email.m'), 'Top domain name is too short');
        $this->assertFalse($validator->validate('+valid@email.ltd'), 'Empty tag');
        $this->assertFalse($validator->validate('valid@email'), 'No top domain name');
    }

    public function testNumberBetweenValidator() {
        $validator = new NumberBetween('Number should be within the boundaries!',
            -10, 10);
        $this->assertEquals('Number should be within the boundaries!', $validator->getError());
        $this->assertTrue($validator->validate(0), 'Zero :)');
        $this->assertTrue($validator->validate(-10), 'Edge case left');
        $this->assertTrue($validator->validate(10), 'Edge case right');
        $this->assertFalse($validator->validate('string'), 'String passed');
        $this->assertFalse($validator->validate(-20), 'out of bounds left');
        $this->assertFalse($validator->validate(20), 'out of bounds right');
    }
}