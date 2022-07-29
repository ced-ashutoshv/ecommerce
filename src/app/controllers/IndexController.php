<?php
use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

// Languages.
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class IndexController extends Controller {

    public function indexAction() {
        $translation         = $this->getTranslator();
        $this->view->myName  = $translation->query( 'name' );
        $this->view->appName = $translation->query( 'app' );
    }

    /**
     * @return NativeArray
     */
    private function getTranslator(): NativeArray {

        $request = new Request();

        // Ask browser what is the best language.
        $language = $request->getBestLanguage();

        $messages = [];
        
        $translationFile = APP_PATH . '/messages/' . $language . '.php';

        if ( true !== file_exists( $translationFile ) ) {
            $translationFile = APP_PATH . '/messages/en.php';
        }
        
        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);

        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
                'triggerError' => true,
            ]
        );
    }
}