<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">@lang('Product Description')</h5>
    </div>
    <div class="card-body">
        <!-- Description Field -->
        <div class="form-group row mb-3">
            <div class="col-md-3">
                <label for="productDescription" class="form-label">@lang('Description')</label>
            </div>
            <div class="col-md-9">
                <textarea rows="5" class="form-control description-field" name="description" id="productDescription">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>

        <!-- Summary Field -->
        <div class="form-group row">
            <div class="col-md-3">
                <label for="productSummary" class="form-label">@lang('Summary')</label>
            </div>
            <div class="col-md-9">
                <textarea rows="5" class="form-control" name="summary" >{{ old('summary', $product->summary ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
