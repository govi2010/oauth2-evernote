<?php


namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Evernote;

class EvernoteOauth extends AbstractProvider
{
    public $consumerKey;
    public $consumerSecret;
    public $sandbox;
    public $client;
    public $redirectUrl;
    public $authorizeUrl;



    public function __construct(array $options, array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        $this->consumerKey = $options['consumerKey'];
        $this->consumerSecret = $options['consumerSecret'];
        $this->sandbox = $options['sandbox'];
        $this->redirectUrl = $options['redirectUrl'];
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */

    public function getAuthorizationUrl(array $options = [])
    {
        $base = $this->getBaseAuthorizationUrl();
        $parts = parse_url($base);
        parse_str($parts['query'], $query);

        $params = $this->getAuthorizationParameters($options);
        $params['oauth_token'] = $query['oauth_token'];


        $query = $this->getAuthorizationQuery($params);
        $base = strtok($base, '?');
        return $this->appendQuery($base, $query);
    }

    public function getBaseAuthorizationUrl()
    {
        $this->client = new Evernote\Client(array(
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'sandbox' => $this->sandbox
        ));

        $token = $this->client->getRequestToken($this->redirectUrl);

        $_SESSION['oauth_token'] = $token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];

        return $this->client->getAuthorizeUrl($token['oauth_token']);
    }

    public function getAccessToken()
    {

        $this->client = new Evernote\Client(array(
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'sandbox' => $this->sandbox
        ));


        $token = $this->client->getAccessToken(
            $_SESSION['oauth_token'],
            $_SESSION['oauth_token_secret'],
            $_GET['oauth_verifier']
        );
        $token['access_token'] = $token['oauth_token'];
        return new AccessToken($token);
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://sandbox.evernote.com/api/DeveloperToken.action';
    }

    public function getResourceOwner(AccessToken $token)
    {
        $client = new Evernote\Client(array('token' => $token));
        return $client->getNoteStore()->listNotebooks();
//        $userStore = $client->getUserStore();
//        return $userStore->getUser();
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return '';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        // TODO: Implement checkResponse() method.
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        // TODO: Implement createResourceOwner() method.
    }
}