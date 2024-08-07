<?php

/* Created on: 27-Aug-2021
 * Description: Controller for Employee Manage .
 * Created by: Harshita Tripathi
 */

namespace App\Http\Controllers;
 use Stripe\Stripe;
 use Stripe\PaymentIntent;
// use Stripe\Charge;
// use Stripe\Token;
// use Stripe\Account;
// use Stripe\AccountLink;

use Illuminate\Http\Request;


class PaymentController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
    }

    public static function createCustomerOnStripe($email)
    {
        try {
            //$data = $request->all();
            $email = $email;

          

            // Get stripe secret key from your constants file
            $stripe_secret = env('STRIPE_SECRET');

           

            // create a customer on stripe for the requested email
            $stripe = new \Stripe\StripeClient(
                $stripe_secret
            ); 

            $customer = $stripe->customers->create([
                'email' => $email,
                'description' => 'customer ' . $email,
            ]);

           

            return $customer;
        } catch (\Exception $e) {
            
            return false;
        }
    }

    public static function createCardToken($card_number, $exp_month, $exp_year, $cvc ,$name)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            $card_token_response = $stripe->tokens->create([
                'card' => [
                    'number' => $card_number,
                    'exp_month' => $exp_month,
                    'exp_year' => $exp_year,
                    'cvc' => $cvc,
                    'name' => $name,
                ],
            ]);

          return $card_token_response;

          //  return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'Card saved successfully.','data'=>$card_token_response], 200);

        } catch (\Exception $e) {
           // return $e->getMessage();

            
            return response()->json([ 'status_code' => 400,'status' => false, 'message' => 'Card not saved successfully.','data'=>$e->getMessage()], 200);

        }
    }


    public static function saveCard($customer_id, $card_token)
    {
        try {
            // Get stripe secret key from your constants file        
            $stripe_secret = env('STRIPE_SECRET');

            $stripe = new \Stripe\StripeClient(
                $stripe_secret
            );
            // create and save card on stripe
            $card_response = $stripe->customers->createSource(
                $customer_id,
                [
                    'source' => $card_token
                ]
            );
            // From the response you can store the partial card detail on your local DB also
            return $card_response;
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function getAllSavedCard($customer_id)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            $card_response = $stripe->customers->allSources(
                $customer_id,
                ['object' => 'card', 'limit' => 10]
            );

            return $card_response;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteCard($customer_id, $card_id)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            $card_response = $stripe->customers->deleteSource(
                $customer_id,
                $card_id,
                []
            );

            return $card_response;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateCard($customer_id,$card_id, $exp_month, $exp_year)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            $card_token_response = $stripe->customers->updateSource(
                $customer_id,
                $card_id,
                [
                    //'number' => $card_number,
                    'exp_month' => $exp_month,
                    'exp_year' => $exp_year,
                    //'cvc' => $cvc,
                ]
              );

            return $card_token_response;
        } catch (\Exception $e) {
            echo $e->getMessage();  die;
            return false;
        }
    }

    public static function addBankAccount($customer_id,$routing_number,$account_number,$account_holder_name)
    {
        try {
            // Initialize the Stripe API client
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Create an Express account
            $account = Account::create([
                'type' => 'express',
                'business_type' => 'individual',
                'email' => 'k.pareek@mtoag.com',
                'requested_capabilities' => ['card_payments', 'transfers'],
            ]);

            // Generate an onboarding link
            $link = AccountLink::create([
                'account' => $account->id,
                'failure_url' => 'https://example.com/failure',
                'success_url' => 'https://example.com/success',
                'type' => 'account_onboarding',
            ]);

            $onboarding_link = $link->url;
            echo $onboarding_link; die;
            /*
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            // Initialize the Stripe API client
            Stripe::setApiKey(env('STRIPE_SECRET'));
            
            $customer = $stripe->customers->retrieve($customer_id);

            $token = Token::create([
                'bank_account' => [
                    'country' => 'US',
                    'currency' => 'usd',
                    'routing_number' => $routing_number,
                    'account_number' => $account_number,
                    'account_holder_name' => $account_holder_name,
                    'account_holder_type' => 'individual',
                ],
            ]);


            $bank_account = \Stripe\Customer::createSource(
                $customer_id,
                ['source' => $token->id]
            );

            echo "<pre>"; print_r($bank_account); die;

            //echo "<pre>"; print_r($customer); die;
            $bankAccount = $customer->sources->create([
                'source' => [
                    'object' => 'bank_account',
                    'country' => 'US',
                    'currency' => 'usd',
                    'routing_number' => $routing_number,
                    'account_number' => $account_number,
                    'account_holder_name' => $account_holder_name,
                    'account_holder_type' => 'individual',
                ],
            ]);
            */
            
            /* $stripe->customers->sources->create([
                'source' => [
                  'object' => 'bank_account',
                  'country' => 'US',
                  'currency' => 'usd',
                  'routing_number' => $request->routing_number,
                  'account_number' => $request->account_number,
                  'account_holder_name' => $request->account_holder_name,
                  'account_holder_type' => 'individual',
                ],
              ]);
             
              
              
              
              
              
            $bankAccount = BankAccount::create([
                'customer' => $customer_id,
                'country' => 'US',
                'currency' => 'usd',
                'routing_number' => $routing_number,
                'account_number' => $account_number,
                'account_holder_name' => $account_holder_name,
                'account_holder_type' => 'individual',
            ]); */

            return $bankAccount;
        }catch (\Exception $e) {
            echo $e->getMessage();  die;
            return false;
        }
    }

 /******* Pranav Raj  Refund Api
             ********/

    public static function refundOrder($transactionId)
    {
      Stripe::setApiKey(env('STRIPE_SECRET'));

      $paymentIntent = PaymentIntent::retrieve($transactionId);
      $chargeId = $paymentIntent->latest_charge;
     
        try {
            $refund = \Stripe\Refund::create([
                'charge' =>  $chargeId,
                //'amount' => 123*100
            ]);
            return $refund;

        } catch (\Exception $e) {
           // echo $e->getMessage();  die;
            return false;
        }
    }


}
