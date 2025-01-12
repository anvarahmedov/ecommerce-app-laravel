public function mount(int|string $record): void {
        $this->record = Product::findOrFail($record);
        $this->form->fill([
            'variation_types' => $this->record->variation_types->map(function($variation_type) {
                return [
                    'name' => $variation_type->name,
                    'option' => $variation_type->options
                ];
            })->toArray(),
            'variations' => $this->record->variations->map(function($variation) {
                return [
                    'id' => $variation->id,
                    'quantity' => $variation->quantity,
                    'price' => $variation->price
                ];
            })->toArray()
        ]);
    }
