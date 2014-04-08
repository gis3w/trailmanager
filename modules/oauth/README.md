OAuth2 client for Kohana
==========================

This module comes with a lightweight PHP wrapper for OAut 2.0, also including customized clients for OAuth providers like Google,
Facebook and GitHub. Implementing a client for another provider is very simple (check out the existing providers).

## How to use it  
This code will be very similar for any OAuth provider.

    $client_id      = YOUR-CLIENT-ID;
    $client_secret  = YOUR-CLIENT-SECRET;
    $redirect_uri   = YOUR-REDIRECT-URI

    // Create facebook client
    $facebook_client = OAuth2_Client::factory('Facebook', $client_id, $client_secret);

    if ( ! isset($_GET['code']))
    {
        // Get the authorization url
        $auth_url = $facebook_client->get_authentication_url($redirect_uri, array(
            'scope' => 'email'
        ));

        // Redirect to the authorization url
        $this->redirect($auth_url);
    }
    else
    {
        // We already have an authorization code, let's get an access token with it
        $params = array(
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri
        );

        $access_token = $facebook_client->get_access_token(OAuth2_Client::GRANT_TYPE_AUTHORIZATION_CODE, $params);
        $facebook_client->set_access_token($access_token);

        /**
         * Get user data
         */
        $user_data = $facebook_client->get_user_data();
        var_dump($user_data);
        

## Licence

Licensed under the MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

