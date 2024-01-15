<?php

namespace Modules\Users\Presenters;

use Modules\Users\Transformers\UserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

class UserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}
