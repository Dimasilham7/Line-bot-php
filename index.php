<?php
    require __DIR__ . '/vendor/autoload.php';
     
    use \LINE\LINEBot;
    use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
    use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
    use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
    use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
    use \LINE\LINEBot\MessageBuilder\TemplateBuilder;
    use \LINE\LINEBot\SignatureValidator as SignatureValidator;
    use LINE\LINEBot\Constant\ActionType;
    use LINE\LINEBot\Constant\MessageType;
    use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
    use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
    use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
    use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
    use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
    use LINE\Tests\LINEBot\Util\DummyHttpClient;
    use PHPUnit\Framework\TestCase; 
    
    // set false for production
    $pass_signature = true;
     
    // set LINE channel_access_token and channel_secret
    $channel_access_token = "";
    $channel_secret = "";
     
    // inisiasi objek bot
    $httpClient = new CurlHTTPClient($channel_access_token);
    $bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
     
    $configs =  [
        'settings' => ['displayErrorDetails' => true],
    ];
    $app = new Slim\App($configs);
     
    // buat route untuk url homepage
    $app->get('/', function($req, $res)
    {
      return "lanjutkan!";
    });
     
    // buat route untuk webhook
    $app->post('/webhook', function ($request, $response) use ($bot, $pass_signature, $httpClient)
    {
        // get request body and line signature header
        $body        = file_get_contents('php://input');
        $signature = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';
     
        // log body and signature
        file_put_contents('php://stderr', 'Body: '.$body);
     
        if($pass_signature === false)
        {
            // is LINE_SIGNATURE exists in request header?
            if(empty($signature)){
                return $response->withStatus(400, 'Signature not set');
            }
     
            // is this request comes from LINE?
            if(! SignatureValidator::validateSignature($body, $channel_secret, $signature)){
                return $response->withStatus(400, 'Invalid signature');
            }
        }
     
        // kode aplikasi nanti disini
        $data = json_decode($body, true);
        foreach ($data['events'] as $event)
        {

            //flex message pilih Diskon
            $userMessage = $event['message']['text'];
            if($userMessage == "Diskon"){
            $flexTemplate = file_get_contents("flex_message.json"); // template flex message
                            $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                'replyToken' => $event['replyToken'],
                                'messages'   => [
                                    [
                                        'type'     => 'flex',
                                        'altText'  => 'Diskon',
                                        'contents' => json_decode($flexTemplate)
                                    ]
                                ],
                            ]);
                            return $response->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                            }

            //carousel cara pembayaran
            $userMessage = $event['message']['text'];
        if($userMessage == "Cara Pembayaran"){
            $ImageCarouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder([
              new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder("https://img.freepik.com/free-vector/payment-methods_1085-813.jpg?size=338&ext=jpg",
              new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Buka Browser',"https://goo.gl/niGrVq")),
              
              ]);
            $templateMessage = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Tata cara pembayaran yang anda harus ikuti',$ImageCarouselTemplateBuilder);
            $result = $bot->replyMessage($event['replyToken'], $templateMessage);
            return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }

       //Carousel Costumer service
            $userMessage = $event['message']['text'];
        if($userMessage == "Costumer Service"){
            $ImageCarouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder([
              new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder("https://png.pngtree.com/element_origin_min_pic/16/11/30/50603ba434026db0a37beb80260b14e1.jpg",
              new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Hubungi CS',"https://api.whatsapp.com/send?phone=6289633767547")),
              
              ]);
            $templateMessage = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Costumer Service yang dapat anda hubungi',$ImageCarouselTemplateBuilder);
            $result = $bot->replyMessage($event['replyToken'], $templateMessage);
            return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }

            //carousel tokopedia
            $userMessage = $event['message']['text'];
        if($userMessage == "tokopedia"){
            $ImageCarouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder([
              new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder("https://pbs.twimg.com/profile_images/1074565719788941312/A7aAWB3E_400x400.jpg",
              new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Tokopedia',"https://www.tokopedia.com/fraggamingstore")),
              
              ]);
            $templateMessage = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Link yang dapat anda kunjungi',$ImageCarouselTemplateBuilder);
            $result = $bot->replyMessage($event['replyToken'], $templateMessage);
            return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }

            //carousel promo
            $userMessage = $event['message']['text'];
            if($userMessage == "Promo"){
                $carouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder([
                  new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("Promo Keyboard", "keyboard yang bisa kamu dapat","https://ecs7.tokopedia.net/img/cache/700/product-1/2018/9/1/1471759/1471759_bdf691c1-d3ae-4666-88a9-4ca6efa0c945.jpg",[
                  new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Cek',"https://goo.gl/8XmcqA"),
                  ]),
                  new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("Promo Headset", "Headset terbaik yang bisa kamu dapat","https://s.blanja.com/picspace/-1/-1/980.389_b76fd617ef46420797b27d8a43dbba79.jpg",[
                  new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Cek',"https://goo.gl/zFQ91t"),
                  ]),
                  ]);
                $templateMessage = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('promo yang bisa kamu dapat',$carouselTemplateBuilder);
                $result = $bot->replyMessage($event['replyToken'], $templateMessage);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
                }
            
                //carousel sale
                $userMessage = $event['message']['text'];
                if($userMessage == "Sale"){
                    $carouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder([
                      new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("sale tahun baru", "rayakan tahun baru-mu dengan gaming gear yang menarik","https://ecs7.tokopedia.net/img/cache/700/product-1/2018/9/1/1471759/1471759_bdf691c1-d3ae-4666-88a9-4ca6efa0c945.jpg",[
                      new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Cek',"https://goo.gl/8XmcqA"),
                      ]),
                      new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("Sale imlek", "rayakan imlek mu dengan gaming gear yang baru!","https://s.blanja.com/picspace/-1/-1/980.389_b76fd617ef46420797b27d8a43dbba79.jpg",[
                      new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Cek',"https://goo.gl/zFQ91t"),
                      ]),
                      ]);
                    $templateMessage = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('sale yang bisa kamu dapatkan',$carouselTemplateBuilder);
                    $result = $bot->replyMessage($event['replyToken'], $templateMessage);
                    return $result->getHTTPStatus() . ' ' . $result->getRawBody();
                    }

        }
        
        
    });

     //mendapatkan profile user yang ada
     $app->get('/profile', function($req, $res) use ($bot)
     {
         // get user profile
         $userId = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
         $result = $bot->getProfile($userId);
        
         return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
     });
 
     //untuk content

     $app->get('/content/{messageId}', function($req, $res) use ($bot)
     {
         // get message content
         $route      = $req->getAttribute('route');
         $messageId = $route->getArgument('messageId');
         $result = $bot->getMessageContent($messageId);
      
         // set response
         $res->write($result->getRawBody());
      
         return $res->withHeader('Content-Type', $result->getHeader('Content-Type'));
     });
     
     //Imagemap
     

    $app->run();