<?php

namespace Abtechi\Laravel\Controller;

use Abtechi\Laravel\Application\AbstractApplication;
use Abtechi\Laravel\Validator\AbstractValidator;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbstractControllerApi extends Controller
{

    protected $application;

    protected $validator = AbstractValidator::class;

    /**
     * AbstractControllerApi constructor.
     * @param $application
     */
    public function __construct(AbstractApplication $application)
    {
        $this->application = $application;

        if (!$this->validator) {
            throw new \InvalidArgumentException('Obrigatório informar o Validator da requisição');
        }
    }

    /**
     * Lista um ou todos os registros
     * @param null $uuid
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listar($uuid = null, Request $request)
    {
        $request->merge([
            'uuid' => $uuid
        ]);

        $result = $this->application->findAll($request);

        if ($result->isResult()) {
            return response()->json($result->getData(), 200);
        }

        return response()->json($result, 400);
    }

    /**
     * Visualizar um determinado registro
     * @param null $uuid
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function visualizar($uuid = null, Request $request)
    {
        $result = $this->application->visualizar($uuid, $request);

        if ($result->isResult()) {
            return response()->json($result->getData(), 200);
        }

        return response()->json($result->getData(), 404);
    }

    /**
     * Inclui novo registro
     * @param Request $request
     * @return mixed
     */
    public function incluir(Request $request)
    {
        $validate = Validator::make($request->all(), $this->validator::$rules, $this->validator::$messages);

        if ($validate->fails()) {
            return response($validate->messages()->toArray(), 400);
        }

        $result = $this->application->create($request);

        if (!$result->isResult()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 201);
        }

        return response()->json(null, 204);
    }

    /**
     * Editar um registro
     * @param $uuid
     * @param Request $request
     * @return mixed
     */
    public function editar($uuid, Request $request)
    {
        $validate = Validator::make($request->all(), $this->validator::$rules, $this->validator::$messages);

        if ($validate->fails()) {
            return response($validate->messages()->toArray(), 400);
        }

        $result = $this->application->update($uuid, $request);

        if (!$result->isResult()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 201);
        }

        return response()->json(null, 204);
    }

    /**
     * Exclui um registro
     * @param $uuid
     * @param Request $request
     * @return mixed
     */
    public function excluir($uuid, Request $request)
    {
        $result = $this->application->delete($uuid, $request);

        if (!$result->isResult() && !$result->getMessage()) {
            return response('', 404);
        }

        if (!$result->isResult() && $result->getMessage()) {
            return response()->json((array)$result, 400);
        }

        return response()->json($result->getData(), 204);
    }

    /**
     * Recupera a listagem em formato de options json: [chave => valor]
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listarOptions(Request $request)
    {
        $result = $this->application->listOptions($request);

        if (!$result->isResult()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 200);
        }

        return response()->json(null, 200);
    }
}