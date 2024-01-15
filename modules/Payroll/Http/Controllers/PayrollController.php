<?php

namespace Modules\Payroll\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Modules\Payroll\Events\SendEmailPayslip;
use Modules\Payroll\Http\Requests\PayslipGenerateRequest;
use Modules\Payroll\Http\Requests\PayslipImportRequest;
use Modules\Payroll\Models\SalarySlip;
use Modules\Payroll\Services\PayrollService;
use Modules\Payroll\Transformers\PayslipTransformer;
use Mpdf\Mpdf;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
        $this->middleware('permission:payslips.view')->only(['index']);
        $this->middleware('permission:payslips.create')->only('generatePaySlips');
        $this->middleware('permission:payslips.edit')->only(['update, payPaySlips']);
    }

    public function index(Request $request)
    {
        $data = $this->payrollService->getPayslips($request->all());

        return responder()->success($data, PayslipTransformer::class)->respond();
    }

    public function update(Request $request, $id)
    {
        $this->payrollService->updatePayslip($request->all(), $id);

        return responder()->success()->respond();
    }

    public function generatePaySlips(PayslipGenerateRequest $request)
    {
        $data = $this->payrollService->generatePaySlips($request->all());

        return responder()->success($data)->respond();
    }

    public function payPaySlips(Request $request)
    {
        $this->payrollService->payPaySlips($request->all());

        return responder()->success()->respond();
    }

    public function payslipSigned($id, Request $request)
    {
        return URL::temporarySignedRoute('payslip_pdf', now()->addMinutes(60), $request->all());
    }

    public function payslip(Request $request)
    {
        $mpdf = $this->viewPayslip($request->all());

        $name = 'payslip.pdf';
        $buffer = $mpdf->Output($name, 'S');

        return $this->bufferDownload($buffer, $name);
    }

    public function sendMailPayslip(Request $request)
    {
        $month = $request->month;
        $payslips = SalarySlip::query()->with('employee.user')->whereIn('id', $request->ids)->get();

        foreach ($payslips as $payslip) {
            $employee = $payslip->employee;
            $requestView = $request->all();
            $requestView['id'] = $payslip->id;
            $data = $this->viewPayslip($requestView);
            $pdf = $data->Output('payslip.pdf', 'S');
            $directory = 'public/payslips';
            $filename = Carbon::now()->format('YmdHis').'_payslip.pdf';
            $path = storage_path("app/$directory");
            File::ensureDirectoryExists($path);
            $file = "$path/$filename";
            file_put_contents($file, $pdf);

            event(new SendEmailPayslip($employee, $file, $month));
        }

        return responder()->success($payslips)->respond();
    }

    private function viewPayslip($request)
    {
        $data = $this->payrollService->exportPayslip($request['id']);
        $employee = $data['employee'];
        $params = $request;

        $mpdf = new Mpdf([
            'format' => 'A4',
            'tempDir' => storage_path('app/public/temp'),
        ]);

        $html = view('payroll::exports.payslip', compact('data', 'employee', 'params'));
        $mpdf->WriteHTML($html->render());

        return $mpdf;
    }

    public function importPayslips(PayslipImportRequest $request)
    {
        return $this->payrollService->importPayslips($request);
    }
}
