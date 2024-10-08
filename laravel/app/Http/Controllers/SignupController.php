<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\MailingList;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\MemberWorkExperience;
use App\Models\Title;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Monarobase\CountryList\CountryListFacade;

class SignupController extends Controller
{
    /**
     * Show the form for sign up of new member.
     */
    public function index(Request $request, Member $member): Response
    {
        $this->authorize('view', $member);

        return Inertia::render('Members/Signup', [
            'member' => $member,
            'completion' => $member->getCompletionsAttribute(),
            'options' => [
                'membership_type_options' => MembershipType::all(['id', 'code', 'title']),
                'gender_options' => Gender::all(['id', 'code', 'title']),
                'title_options' => Title::all(['id', 'code', 'title']),
                'mailing_options' => MailingList::all(['id', 'code', 'title']),
            ],
            'qualifications' => $member->qualifications()->orderBy('year_attained', 'desc')->get(),
            'referees' => $member->referees()->get(),
            'memberWorkExperiences' => MemberWorkExperience::select(
                'id',
                'organisation',
                'position',
                'responsibilities',
                'from_date',
                'to_date',
                'is_current',
            )
                ->where('member_id', $member->id)
                ->orderBy('from_date', 'desc')
                ->get(),
            'supportingDocuments' => $member->supportingDocuments()
                ->where('to_delete', false)
                ->get(['id', 'title', 'file_name', 'file_size']),
            'memberMailingLists' => $member->mailingLists()->get(['mailing_list_id', 'subscribed']),
            'countryList' => CountryListFacade::getList(),
            'tab' => $request->get('tab'),
        ]);
    }
}
