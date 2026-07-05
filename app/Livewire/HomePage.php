<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AppReview;
use App\Models\Product;

class HomePage extends Component
{
    // Properti untuk form ulasan
    public string $reviewer_name = '';
    public int $rating = 5;
    public string $comment = '';

    public function submitReview()
    {
        $this->validate([
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Mass-assignment only allows these 3 columns (see AppReview::$fillable),
        // and Blade's {{ }} auto-escapes output, so stored HTML/JS cannot execute
        // when the review is rendered back on the page.
        AppReview::create([
            'reviewer_name' => $this->reviewer_name,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->reset(['reviewer_name', 'rating', 'comment']);
        session()->flash('message', 'Terima kasih atas ulasan Anda!');
    }

    public function render()
    {
        $products = Product::with('store')->latest()->take(6)->get();

        if ($products->isEmpty()) {
            // Dummy fallback only used before any Seller has listed real products.
            $products = collect([
                (object) ['id' => null, 'name' => 'Sepatu Lari Mekanik', 'price' => 450000, 'store' => (object) ['name' => 'Toko Lari Cepat']],
                (object) ['id' => null, 'name' => 'Keyboard Mekanikal', 'price' => 850000, 'store' => (object) ['name' => 'TechGear ID']],
                (object) ['id' => null, 'name' => 'Kopi Arabika 250g', 'price' => 65000, 'store' => (object) ['name' => 'Kopi Senja']],
            ]);
        }

        $reviews = AppReview::latest()->take(5)->get();

        return view('components.home-page', [
            'products' => $products,
            'reviews' => $reviews,
        ]);
    }
}
