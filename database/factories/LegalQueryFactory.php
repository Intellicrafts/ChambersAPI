<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegalQuery>
 */
class LegalQueryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questions = [
            'What are my rights if I\'ve been wrongfully terminated from my job?',
            'How do I file for divorce in this state?',
            'What should I do if I\'ve been in a car accident?',
            'How can I protect my intellectual property?',
            'What are the steps to create a will?',
            'How do I handle a landlord-tenant dispute?',
            'What are the legal implications of starting a business?',
            'How do I file a personal injury claim?',
            'What should I know about child custody laws?',
            'How do I deal with debt collectors?',
            'What are my options for bankruptcy?',
            'How do I handle a contract breach?',
            'What are the legal requirements for immigration?',
            'How do I file a discrimination complaint?',
            'What should I do if I receive a traffic ticket?'
        ];
        
        $responses = [
            'Based on the information provided, you may have a valid claim. It\'s recommended to gather all documentation related to your employment and termination.',
            'The process varies by jurisdiction, but generally involves filing a petition, serving your spouse, and attending court hearings.',
            'First, ensure everyone\'s safety and exchange insurance information. Document the scene and seek medical attention if needed.',
            'You can protect your intellectual property through patents, trademarks, copyrights, or trade secrets depending on the nature of your creation.',
            'Creating a will typically involves listing your assets, naming beneficiaries, appointing an executor, and having the document witnessed and notarized.',
            'Review your lease agreement and local tenant laws. Document all communications with your landlord and consider mediation before litigation.',
            'Business formation involves choosing a structure (LLC, corporation, etc.), registering with state agencies, obtaining licenses, and understanding tax obligations.',
            'Personal injury claims typically require proving negligence, documenting injuries, calculating damages, and filing within the statute of limitations.',
            'Child custody decisions are based on the best interests of the child, considering factors like parental capability, stability, and the child\'s relationship with each parent.',
            'Know your rights under the Fair Debt Collection Practices Act. Request debt verification and consider negotiating a payment plan or settlement.',
            'Bankruptcy options include Chapter 7 (liquidation) and Chapter 13 (reorganization). Each has different eligibility requirements and consequences.',
            'Document the breach, review the contract\'s dispute resolution provisions, send a demand letter, and consider mediation before litigation.',
            'Immigration processes vary based on your situation (family-based, employment-based, asylum, etc.) and typically involve filing petitions, providing documentation, and attending interviews.',
            'File with the appropriate agency (EEOC for employment discrimination), provide evidence of discriminatory treatment, and adhere to filing deadlines.',
            'Options include paying the fine, contesting the ticket in court, attending traffic school, or hiring an attorney if the consequences are severe.'
        ];
        
        $randomIndex = array_rand($questions);
        
        return [
            'id' => fake()->uuid(),
            'user_id' => null, // Will be set in the seeder
            'question_text' => $questions[$randomIndex],
            'ai_response' => $responses[$randomIndex],
        ];
    }
}
