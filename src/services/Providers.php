<?php
namespace verbb\socialshare\services;

use verbb\socialshare\SocialShare;
use verbb\socialshare\base\ProviderInterface;
use verbb\socialshare\providers as providerTypes;

use Craft;
use craft\base\Component;
use craft\base\MemoizableArray;
use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\Component as ComponentHelper;
use craft\helpers\ProjectConfig as ProjectConfigHelper;

class Providers extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_PROVIDER_TYPES = 'registerProviderTypes';


    // Properties
    // =========================================================================

    private ?MemoizableArray $_providers = null;


    // Public Methods
    // =========================================================================

    public function getAllProviderTypes(): array
    {
        $providerTypes = [
            providerTypes\Airbnb::class,
            providerTypes\AngelList::class,
            providerTypes\AppleMusic::class,
            providerTypes\AppStore::class,
            providerTypes\Bandcamp::class,
            providerTypes\Behance::class,
            providerTypes\Bitbucket::class,
            providerTypes\Blogger::class,
            providerTypes\Buffer::class,
            providerTypes\CodePen::class,
            providerTypes\DeviantArt::class,
            providerTypes\Diaspora::class,
            providerTypes\Digg::class,
            providerTypes\Discord::class,
            providerTypes\Discourse::class,
            providerTypes\Douban::class,
            providerTypes\Dribbble::class,
            providerTypes\Dropbox::class,
            providerTypes\Email::class,
            providerTypes\Envato::class,
            providerTypes\Etsy::class,
            providerTypes\Evernote::class,
            providerTypes\Facebook::class,
            providerTypes\Feedly::class,
            providerTypes\Figma::class,
            providerTypes\FiveZeroZeroPx::class,
            providerTypes\Flickr::class,
            providerTypes\Flipboard::class,
            providerTypes\Foursquare::class,
            providerTypes\FreeCodeCamp::class,
            providerTypes\Gab::class,
            providerTypes\Gettr::class,
            providerTypes\GitHub::class,
            providerTypes\Gmail::class,
            providerTypes\GoodReads::class,
            providerTypes\GooglePlay::class,
            providerTypes\HackerNews::class,
            providerTypes\Houzz::class,
            providerTypes\Instagram::class,
            providerTypes\Instapaper::class,
            providerTypes\Intercom::class,
            providerTypes\Iorbix::class,
            providerTypes\JsFiddle::class,
            providerTypes\Kakao::class,
            providerTypes\Kickstarter::class,
            providerTypes\Kik::class,
            providerTypes\KindleIt::class,
            providerTypes\Kooapp::class,
            providerTypes\LastFm::class,
            providerTypes\Line::class,
            providerTypes\LinkedIn::class,
            providerTypes\LiveJournal::class,
            providerTypes\Mailchimp::class,
            providerTypes\Mailru::class,
            providerTypes\Mastodon::class,
            providerTypes\Medium::class,
            providerTypes\Meetup::class,
            providerTypes\Meneame::class,
            providerTypes\Messenger::class,
            providerTypes\MeWe::class,
            providerTypes\Mix::class,
            providerTypes\MixCloud::class,
            providerTypes\Odnoklassniki::class,
            providerTypes\Outlook::class,
            providerTypes\Parler::class,
            providerTypes\Patreon::class,
            providerTypes\Phone::class,
            providerTypes\Pinterest::class,
            providerTypes\Pocket::class,
            providerTypes\PrintProvider::class,
            providerTypes\ProductHunt::class,
            providerTypes\Quora::class,
            providerTypes\Qzone::class,
            providerTypes\Reddit::class,
            providerTypes\Refind::class,
            providerTypes\Renren::class,
            providerTypes\Rss::class,
            providerTypes\Shopify::class,
            providerTypes\Skype::class,
            providerTypes\Slack::class,
            providerTypes\Sms::class,
            providerTypes\Snapchat::class,
            providerTypes\SoundCloud::class,
            providerTypes\Spotify::class,
            providerTypes\Squarespace::class,
            providerTypes\StackExchange::class,
            providerTypes\StackOverflow::class,
            providerTypes\Steam::class,
            providerTypes\Strava::class,
            providerTypes\Surfingbird::class,
            providerTypes\Telegram::class,
            providerTypes\Tencentqq::class,
            providerTypes\Threema::class,
            providerTypes\TikTok::class,
            providerTypes\Trello::class,
            providerTypes\TripAdvisor::class,
            providerTypes\Tumblr::class,
            providerTypes\Twitch::class,
            providerTypes\Twitter::class,
            providerTypes\Unsplash::class,
            providerTypes\Viber::class,
            providerTypes\Vimeo::class,
            providerTypes\Vkontakte::class,
            providerTypes\WeChat::class,
            providerTypes\Weibo::class,
            providerTypes\WhatsApp::class,
            providerTypes\Wikipedia::class,
            providerTypes\Wordpress::class,
            providerTypes\Xing::class,
            providerTypes\Yammer::class,
            providerTypes\Yelp::class,
            providerTypes\YouTube::class,
            providerTypes\Yummly::class,
            providerTypes\Zillow::class,
        ];

        $event = new RegisterComponentTypesEvent([
            'types' => $providerTypes,
        ]);
        $this->trigger(self::EVENT_REGISTER_PROVIDER_TYPES, $event);

        return $event->types;
    }

    public function createProvider(mixed $config): ProviderInterface
    {
        if (is_string($config)) {
            $config = ['type' => $config];
        }

        try {
            $provider = ComponentHelper::createComponent($config, ProviderInterface::class);
        } catch (MissingComponentException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);

            $provider = new providerTypes\MissingProvider($config);
        }

        return $provider;
    }

    public function getAllProviders(): array
    {
        return $this->_providers()->all();
    }

    public function getAllFollowersCountProviders(): array
    {
        $providers = [];

        foreach ($this->getAllProviders() as $provider) {
            if ($provider::supportsFollowersCount()) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    public function getAllSharesCountProviders(): array
    {
        $providers = [];

        foreach ($this->getAllProviders() as $provider) {
            if ($provider::supportsSharesCount()) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    public function getAllShareButtonProviders(): array
    {
        $providers = [];

        foreach ($this->getAllProviders() as $provider) {
            if ($provider::supportsShareButton()) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    public function getProviderByHandle(string $handle): ?ProviderInterface
    {
        return $this->_providers()->firstWhere('handle', $handle, true);
    }

    public function createProviderConfig(ProviderInterface $provider): array
    {
        $settings = $provider->getSettings();

        return ProjectConfigHelper::packAssociativeArrays($settings);
    }

    public function saveProvider(ProviderInterface $provider): bool
    {
        if (!$provider->validate()) {
            return false;
        }

        $settings = SocialShare::$plugin->getSettings();
        $settings->providers[$provider->handle] = $this->createProviderConfig($provider);

        $plugin = Craft::$app->getPlugins()->getPlugin('social-share');

        return Craft::$app->getPlugins()->savePluginSettings($plugin, $settings->toArray());
    }


    // Private Methods
    // =========================================================================

    private function _providers(): MemoizableArray
    {
        $settings = SocialShare::$plugin->getSettings();

        if (!isset($this->_providers)) {
            $providers = [];

            foreach ($this->getAllProviderTypes() as $type) {
                // Fetch settings from the plugin settings
                $config = $settings->providers[$type::$handle] ?? [];
                $config['type'] = $type;

                $providers[] = $this->createProvider($config);
            }

            $this->_providers = new MemoizableArray($providers);
        }

        return $this->_providers;
    }

}
