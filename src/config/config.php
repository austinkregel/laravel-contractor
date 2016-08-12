<?php
return [
    'user_model' => App\User::class,
    'route' => 'contractor', //base route name for the contractor routes.
    'view-base' => 'spark::layouts.app', // Any view that you wish to extend
    /**
     * If you wanted the related model to be a user so you could have contracts related to a given user
     * just set the related_model to your user model. Then set your related_model_key to 'id'.
     */
    'related_model' => App\User::class , // Pass the class name here;)
    'related_model_key' => 'user_id',

    'related_model_name_key' => 'name',

    'user_relationship' => function(){
        // Essentially, this must return the model that has the contractable trait.
        return auth()->user();
    },
    'storage_path' => 'app/contractor/',
    'mail' => [
        'from' => [
            'address' => 'no-reply@somedomain.com',
            'name' => 'Contracting system'
        ],
        'subject' => [
            'new' => [
                'user' => 'Thank you for joining!'
            ]
        ],
        'template' => [
            'new' => [
                'contract' => 'contractor::emails.new-contract'
            ],
            'due' => [
                'contract' => 'contractor::emails.due-contract'
            ],
        ]
    ],
    'middleware' => ['web']
];
