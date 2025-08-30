public function prediksiStunting(Request $request)
{
    $umur = $request->input('umur');
    $berat = $request->input('berat_badan');
    $tinggi = $request->input('tinggi_badan');
    $jk = $request->input('jenis_kelamin'); // L atau P

    $command = "python predict.py $umur $berat $tinggi $jk";
    $output = shell_exec($command);
    $hasil = json_decode($output, true);

    return response()->json([
        'status' => $hasil['prediksi'] == 1 ? 'Stunting' : 'Normal'
    ]);
}
