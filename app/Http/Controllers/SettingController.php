<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AhpCriteria;
use App\Models\FuzzyRule;

class SettingController extends Controller
{
    public function fuzzyIndex()
    {
        $criteria = AhpCriteria::all();
        $rules = FuzzyRule::all();
        
        return view('settings.fuzzy', compact('criteria', 'rules'));
    }

    public function updateCriteria(Request $request)
    {
        $request->validate([
            'criteria' => 'required|array',
            'criteria.*.id' => 'required|exists:ahp_criteria,id',
            'criteria.*.weight' => 'required|numeric|min:0|max:1',
            'criteria.*.is_active' => 'boolean',
        ]);

        foreach ($request->criteria as $criteriaData) {
            AhpCriteria::where('id', $criteriaData['id'])
                ->update([
                    'weight' => $criteriaData['weight'],
                    'is_active' => $criteriaData['is_active'] ?? true,
                ]);
        }

        return redirect()->route('settings.fuzzy')
            ->with('success', 'Bobot kriteria AHP berhasil diperbarui');
    }

    public function updateRules(Request $request)
    {
        $request->validate([
            'rules' => 'required|array',
            'rules.*.id' => 'required|exists:fuzzy_rules,id',
            'rules.*.weight' => 'required|numeric|min:0|max:1',
            'rules.*.is_active' => 'boolean',
        ]);

        foreach ($request->rules as $ruleData) {
            FuzzyRule::where('id', $ruleData['id'])
                ->update([
                    'weight' => $ruleData['weight'],
                    'is_active' => $ruleData['is_active'] ?? true,
                ]);
        }

        return redirect()->route('settings.fuzzy')
            ->with('success', 'Aturan fuzzy berhasil diperbarui');
    }
}