# Cake3 Upload
A Cake3 plugin to Upload files.

[![Build Status](https://img.shields.io/travis/Xety/Cake3-Upload.svg?style=flat-square)](https://travis-ci.org/Xety/Xeta)
[![Coverage Status](https://img.shields.io/coveralls/Xety/Cake3-Upload/master.svg?style=flat-square)](https://coveralls.io/r/xety/xeta)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/Xety/Cake3-Upload.svg?style=flat-square)](https://scrutinizer-ci.com/g/Xety/Xeta)
[![Latest Stable Version](https://img.shields.io/packagist/v/Xety/Cake3-Upload.svg?style=flat-square)](https://packagist.org/packages/xety/xeta)
[![Total Downloads](https://img.shields.io/packagist/dt/xety/cake3-upload.svg?style=flat-square)](https://packagist.org/packages/xety/xeta)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://packagist.org/packages/xety/xeta)

## Requirements
* CakePHP 3.X

## Installation
Run : `composer require xety/cake3-upload:1.*`
Or add it in your `composer.json`:
``` php
"require": {
	"xety/cake3-upload": "1.*"
},
```

## Usage
In your `config/bootstrap.php` add :
``` php
Plugin::load('Xety/Cake3Upload');
```
In your model `initialize()`:
``` php
$this->addBehavior('Xety/Cake3Upload', [
		'fields' => [
			'avatar' => [
				'path' => 'upload/avatar/:id/:md5'
			]
		]
	]
);
```

### Identifiers
* **:id** Id of the Entity (It can be the user Id if you are using this for the users table for example)
* **:md5** A random and unique identifier with 32 characters. i.e : *bbebb3c3c5e76a46c3dca92c9395ee65*
* **:y** Based on the current year. i.e : *2014*
* **:m** Based on the current month. i.e : *09*

To create an input to upload a file, just use the this rule : **fieldName_file**. Example :
``` php
<?= $this->Form->input('avatar_file', ['type' => 'file']) ?>
```

## Configuration
* ### suffix
    Default : `_file`

    You can chage the suffix *_file* to your own suffix :
    ``` php
    $this->addBehavior('Upload', [
    		'fields' => [
    			'avatar' => [
    				'path' => 'upload/avatar/:id/:md5'
    			]
    		],
    		'suffix' => '_anotherName'
    	]
    );

    <?= $this->Form->input('avatar_anotherName', ['type' => 'file']) ?>
    ```

* ### overwrite
    Default : `true`

    This option allow you to define if the behavior must delete and/or overwrite the old file for the field. **If the option is *false*, the file will be not uploaded if the old file name has the same name as the new name file.** It can be useful if you want your users to upload only one image.
    ``` php
    $this->addBehavior('Upload', [
    		'fields' => [
    			'avatar' => [
    				'path' => 'upload/avatar/:id/:md5',
                    'overwrite' => false
    			]
    		]
    	]
    );
    ```

* ### defaultFile
    Default : `false`

    This option allow you to defined a default file for the field. It can be useful if you have defined a default avatar for all your new user and you don't want to delete it (i.e : In your database as defaut value for avatar you have set : "../img/default_avatar.png"). **Will work only if the overwrite is defined to *true***.
    ``` php
    $this->addBehavior('Upload', [
    		'fields' => [
    			'avatar' => [
    				'path' => 'upload/avatar/:id/:md5',
                    'overwrite' => true,
                    'defaultFile' => 'default_avatar.png'
    			]
    		]
    	]
    );
    ```

* ### prefix
    Default : `false`

    This option allow you to defined a prefix for your upload path. Useful if you don't want to use the img/ directory for your upload.
    ``` php
    $this->addBehavior('Upload', [
    		'fields' => [
    			'avatar' => [
    				'path' => 'upload/avatar/:id/:md5',
    				'prefix' => '../'
    			]
    		]
    	]
    );
    ```
    ##### Example :
    If you use a custom directory at the root of the *webroot* directory and you use the `HtmlHelper` to display your image, you can set a prefix like this :
    ``` php
    /**
     * The path look like this :
     *       webroot/upload/avatar
     *
     * In the database, the record will look like that :
     *      ../upload/avatar/1/bbebb3c3c5e76a46c3dca92c9395ee65.png
     */

    $this->addBehavior('Upload', [
    		'fields' => [
    			'avatar' => [
    				'path' => 'upload/avatar/:id/:md5',
    				'prefix' => '../'
    			]
    		]
    	]
    );

    // In a view, with the Html Helper:
    <?= $this->Html->image($User->avatar) ?>
    // Output : <img src="/img/../upload/avatar/1/bbebb3c3c5e76a46c3dca92c9395ee65.png" alt="">
    ```
