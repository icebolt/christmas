<?php
class ErrorController extends SlatePF\Extras\ExtrasController {

        public function errorAction($exception) {
                $this->assign("exception", $exception);
                $this->display("error/error.phtml");
        }
}
