<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Freelancer;
use App\Models\Thread;

class CompanyFreelancerController extends Controller
{
    /**
     * フリーランス一覧（検索付き）を表示する
     */
    public function index(Request $request)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 企業プロフィールが無い場合は先に登録へ誘導する
        if ($user->company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // 検索キーワードを取得する（keywordはGETクエリ）
        $keyword = $request->query('keyword');

        // フリーランスをベースに検索クエリを作る（プロフィール全体を検索）
        $query = Freelancer::query()->with(['skills', 'customSkills', 'portfolios']);

        // keyword がある場合、LIKE検索をかける（横断検索）
        if (is_string($keyword) && $keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                // 表示名にヒットさせる
                $q->where('display_name', 'like', '%' . $keyword . '%')
                    // 職種にヒットさせる
                    ->orWhere('job_title', 'like', '%' . $keyword . '%')
                    // 自己紹介にヒットさせる
                    ->orWhere('bio', 'like', '%' . $keyword . '%')
                    // 働き方にヒットさせる
                    ->orWhere('work_style_text', 'like', '%' . $keyword . '%')
                    // 経験企業にヒットさせる
                    ->orWhere('experience_companies', 'like', '%' . $keyword . '%')
                    // 希望単価（数値）にもヒットさせる
                    ->orWhereRaw('CAST(min_rate as CHAR) like ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('CAST(max_rate as CHAR) like ?', ['%' . $keyword . '%'])
                    // マスタスキル名にもヒットさせる（JOIN代替: whereHas）
                    ->orWhereHas('skills', function ($sq) use ($keyword) {
                        $sq->where('name', 'like', '%' . $keyword . '%');
                    })
                    // カスタムスキル名にもヒットさせる
                    ->orWhereHas('customSkills', function ($cq) use ($keyword) {
                        $cq->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // 一覧はページングで取得する
        $freelancers = $query->orderByDesc('id')->paginate(20)->withQueryString();

        // 企業IDを取得
        $companyId = $user->company->id;

        // 各フリーランスに対してスカウト済みかどうか（スレッドが存在するか）を確認
        $scoutThreadMap = [];
        foreach ($freelancers as $freelancer) {
            // スカウトスレッド（job_idがnull）を探す
            $thread = Thread::where('company_id', $companyId)
                ->where('freelancer_id', $freelancer->id)
                ->whereNull('job_id')
                ->first();
            
            if ($thread) {
                $scoutThreadMap[$freelancer->id] = $thread->id;
            }
        }

        // 応募に関連するthreadの未読数（企業側）
        $unreadApplicationCount = Thread::query()
            ->where('company_id', $companyId)
            ->whereNotNull('job_id') // 応募はjob_idが必須
            ->where('is_unread_for_company', true)
            ->count();

        // スカウトに関連するthreadの未読数（企業側、job_idがnullのもの）
        $unreadScoutCount = Thread::query()
            ->where('company_id', $companyId)
            ->whereNull('job_id') // スカウトはjob_idがnull
            ->where('is_unread_for_company', true)
            ->count();

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = '企';
        if ($user->company !== null && !empty($user->company->name)) {
            $userInitial = mb_substr($user->company->name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        // 一覧ビューへ返す
        return view('company.freelancers.index', [
            // 表示用の一覧
            'freelancers' => $freelancers,
            // 検索キーワードを保持する
            'keyword' => $keyword,
            // スカウト済みフリーランスのスレッドIDマップ
            'scoutThreadMap' => $scoutThreadMap,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * フリーランス詳細を表示する
     */
    public function show(Freelancer $freelancer)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        // 企業以外は拒否する
        if ($user->role !== 'company') {
            abort(403);
        }

        // 企業プロフィールが無い場合は先に登録へ誘導する
        if ($user->company === null) {
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // 表示用に必要なリレーションを読み込む
        $freelancer->load(['skills', 'customSkills', 'portfolios']);

        $company = $user->company;

        // 応募に関連するthreadの未読数（企業側）
        $unreadApplicationCount = Thread::query()
            ->where('company_id', $company->id)
            ->whereNotNull('job_id') // 応募はjob_idが必須
            ->where('is_unread_for_company', true)
            ->count();

        // スカウトに関連するthreadの未読数（企業側、job_idがnullのもの）
        $unreadScoutCount = Thread::query()
            ->where('company_id', $company->id)
            ->whereNull('job_id') // スカウトはjob_idがnull
            ->where('is_unread_for_company', true)
            ->count();

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = '企';
        if ($company !== null && !empty($company->name)) {
            $userInitial = mb_substr($company->name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        // 詳細ビューへ返す
        return view('company.freelancers.show', [
            // 表示対象のフリーランス
            'freelancer' => $freelancer,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }
}