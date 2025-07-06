public function render($request, Throwable $exception)
{
    if ($request->is('api/*')) {
        return response()->json([
            'error' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
        ], 500);
    }

    return parent::render($request, $exception);
}
