<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 21.11.15
 * Time: 19:06
 */

namespace Waddle\Tests;

use Waddle\Classes\Validators\Email;
use Waddle\Classes\Validators\GreaterThan;
use Waddle\Classes\Validators\LowerThan;
use Waddle\Classes\Validators\MaxLength;
use Waddle\Classes\Validators\NumberBetween;
use Waddle\Classes\Validators\Required;

class ValidatorsTest extends \PHPUnit_Framework_TestCase {

    public function requiredDataProvider() {
        return [
            ['Data', 'Required: String value is required', true],
            ['', 'Required: Empty string', false],
            [10, 'Required: integer value', true],
            [0, 'Required: zero integer, can be zero', true],
            [null, 'Required: null value', false]
        ];
    }

    /**
     * @dataProvider requiredDataProvider
     */
    public function testRequiredValidator($data, $message, $expected) {
        $validator = new Required('The field is required!');
        $this->assertEquals('The field is required!', $validator->getError());
        $this->assertEquals($expected, $validator->validate($data), $message);
        $this->assertFalse((new Required('The field is required!', false))->validate(0), 'Required: can\'t be zero');
    }

    public function emailDataProvider() {
        return [
            ['test@gmail.com', 'Simplest email', true],
            ['test-555.dot@what-a-domain.com', 'Email with dashes and dots', true],
            ['sometag+test-555.dot@what-a-domain.com', 'Email with dashes and dots', true],
            ['username@domain.longtimeago', 'Email with long top domain name', true],
            ['not valid@email.com', 'Spaces in email', false],
            ['valid@email.m', 'Top domain name is too short', false],
            ['+valid@email.ltd', 'Empty tag', false],
            ['valid@email', 'No top domain name', false]
        ];
    }

    /**
     * @dataProvider emailDataProvider
     */
    public function testEmailValidator($data, $message, $expected) {
        $validator = new Email('Enter correct email!');
        $this->assertEquals('Enter correct email!', $validator->getError());
        $this->assertEquals($expected, $validator->validate($data), $message);
    }

    public function numbersBetweenDataProvider() {
        return [
            [0, 'Zero :)', true],
            [-10, 'Edge case left', true],
            [10, 'Edge case right', true],
            ['string', 'String passed', false],
            [-20, 'out of bounds left', false],
            [20, 'out of bounds right', false],
        ];
    }

    /**
     * @dataProvider numbersBetweenDataProvider
     */
    public function testNumberBetweenValidator($data, $message, $expected) {
        $validator = new NumberBetween('Number should be within the boundaries!',
            -10, 10);
        $this->assertEquals('Number should be within the boundaries!', $validator->getError());
        $this->assertEquals($expected, $validator->validate($data), $message);
    }

    public function greaterThanDataProvider() {
        return [
            [10, 'greater than 5.0', true],
            [5, 'integer equals to 5.0', true],
            [5.0, 'float equals to 5.0', true],
            [0, 'less than 5.0', false]
        ];
    }

    /**
     * @dataProvider greaterThanDataProvider
     */
    public function testGreaterThanValidator($data, $message, $expected) {
        $validator = new GreaterThan('Number should be grater than 5.0!', 5.0);
        $this->assertEquals('Number should be grater than 5.0!', $validator->getError());
        $this->assertEquals($expected, $validator->validate($data), $message);
    }

    public function lowerThanDataProvider() {
        return [
            [3, 'lower than 5.0', true],
            [5, 'integer equals to 5.0', true],
            [5.0, 'float equals to 5.0', true],
            [10, 'greater than 5.0', false]
        ];
    }

    /**
     * @dataProvider lowerThanDataProvider
     */
    public function testLowerThanValidator($data, $message, $expected) {
        $validator = new LowerThan('Number should be lower than 5.0!', 5.0);
        $this->assertEquals('Number should be lower than 5.0!', $validator->getError());
        $this->assertEquals($expected, $validator->validate($data), $message);
    }

    public function maxLengthDataProvider() {
        return [
            ['string', 7, 'String with length 6 is shorter than 7', true],
            ['edge case', 9, 'If max length is 9 then string of length 9 is ok', true],
            ['too long', 5, 'String shouldn\'t be too long', false],
            ['', 0, 'Empty strings are ok', true],
            ['юникод, кириллица', 17, 'Cyrillic characters should count properly', true],
            ['بعض سلسلة', 9, 'As well as Arabic', true],
            [1510, 4, 'Numbers are fine as long as their string representation is within bounds', true],
            [-1510, 4, 'Minus sign counts!', false]
        ];
    }

    /**
     * @dataProvider maxLengthDataProvider
     */
    public function testMaxLengthValidator($data, $maxLength, $message, $expected) {
        $validator = new MaxLength('Text should be within the bounds!', 5.0);
        $this->assertEquals('Text should be within the bounds!', $validator->getError());
        $this->assertEquals($expected, (new MaxLength('', $maxLength))->validate($data), $message);
    }
}