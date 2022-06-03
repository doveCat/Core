<?php
namespace DACore\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{

    protected $app_id = 1;
    protected $group_id = 1;

    /**
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $app_id = config('mbcore_mcore.default.app_id',config('mbcore_mpartner.default.app_id',1));
        $group_id = config('mbcore_mcore.default.group_id',config('mbcore_mpartner.default.group_id',1));
        $this->app_id = $request->get("app_id")?:$app_id;
        $this->group_id = $request->get("group_id")?:$group_id;
    }

    /**
     * @param $result
     * @param int $code
     * @param int $httpCode
     * @param null $msg
     */
    protected function ret($result,$code=1, $httpCode=200,$msg=null)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:Origin, X-Requested-With,  Content-Type, Cookie, Accept, appid, channel, mbcore-access-token, mbcore-auth-token');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
        header('Access-Control-Allow-Credentials:false');
        header('Access-Control-Max-Age:3600');
        header('Content-Type:text/html;charset=utf-8');
        header('Content-type:application/json');
        if($httpCode == 403 ){
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'code' => $code,
                'msg' => $result,  // 暂时兼容性保留原样内容
                'result' => ['msg' => $result]
            ]);
            exit;
        }
        # 正常执行
        echo json_encode([
            'code' => $code,
            'result' => $result,
            'msg' => $msg ?: ($result['msg'] ?? null),
        ]);
        exit;
//        return response()->json([
//            'code' => $code,
//            'result' => $msg
//        ],$httpCode,[],271);
    }

    /**
     * @param $result
     * @param $code
     * @param int $httpCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function retMsgString($result,$code=0,$httpCode=200,$msg = null)
    {
        return $this->ret($result,$code,$httpCode,$msg);
    }
}
