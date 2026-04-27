<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class EffectiveDealerImport {

    const NONCE_UPLOAD  = 'effdf_import_upload';
    const NONCE_PREVIEW = 'effdf_import_preview';
    const NONCE_EXECUTE = 'effdf_import_execute';
    const TRANSIENT_KEY = 'effdf_import_csv_';

    /** Known plugin fields available for mapping */
    private function mappableFields(): array {
        return [
            ''                   => '— Skip —',
            'title'              => 'Dealer Name (Title)',
            'dealer_phone'       => 'Phone',
            'dealer_fax'         => 'Fax',
            'dealer_email'       => 'Email',
            'dealer_website'     => 'Website',
            'dealer_location'    => 'Full Address (geocode this)',
            'dealer_address'     => 'Street Address',
            'dealer_address2'    => 'Address Line 2',
            'dealer_city'        => 'City',
            'dealer_state'       => 'State',
            'dealer_postal_code' => 'Postal Code',
            'dealer_country'     => 'Country',
            'dealer_latitude'    => 'Latitude',
            'dealer_longitude'   => 'Longitude',
        ];
    }

    /** Entry point — render the correct step */
    public function render(): void {
        echo '<div class="wrap effdf-import">';
        echo '<h1>Import Dealers</h1>';

        $step = isset( $_POST['effdf_step'] ) ? sanitize_key( $_POST['effdf_step'] ) : 'upload';

        switch ( $step ) {
            case 'preview':
                $this->renderPreviewStep();
                break;
            case 'execute':
                $this->renderExecuteStep();
                break;
            default:
                $this->renderUploadStep();
        }

        echo '</div>';
    }

    // -------------------------------------------------------------------------
    // STEP 1 — Upload
    // -------------------------------------------------------------------------

    private function renderUploadStep(): void {
        $api_key = apply_filters( 'EFFDF_GEOCODING_API_KEY', effdf_get_api_key() );
        if ( empty( $api_key ) ) {
            echo '<div class="notice notice-warning"><p><strong>Warning:</strong> No Google API key is configured. Set <code>EFFDF_GEOCODING_API_KEY</code> (recommended — IP-restricted, Geocoding API only) or <code>EFFDF_GOOGLE_API_KEY</code> as a fallback. Geocoding will be skipped during import.</p></div>';
        }
        ?>
        <p>Upload a CSV file with a <strong>header row</strong>. You will be able to map columns on the next screen.</p>
        <p><a href="<?php echo esc_url( $this->sampleCsvUrl() ); ?>">Download sample CSV template</a></p>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( self::NONCE_UPLOAD, '_wpnonce_import' ); ?>
            <input type="hidden" name="effdf_step" value="preview">
            <table class="form-table">
                <tr>
                    <th><label for="effdf_csv">CSV File</label></th>
                    <td><input type="file" name="effdf_csv" id="effdf_csv" accept=".csv" required></td>
                </tr>
            </table>
            <?php submit_button( 'Upload & Preview' ); ?>
        </form>
        <?php
    }

    private function sampleCsvUrl(): string {
        return add_query_arg([
            'post_type'          => 'dealer',
            'page'               => 'effdf-import',
            'effdf_sample_csv'   => '1',
            '_wpnonce'           => wp_create_nonce( 'effdf_sample_csv' ),
        ], admin_url( 'edit.php' ) );
    }

    // -------------------------------------------------------------------------
    // STEP 2 — Map columns & Preview rows
    // -------------------------------------------------------------------------

    private function renderPreviewStep(): void {
        if ( ! check_admin_referer( self::NONCE_UPLOAD, '_wpnonce_import' ) ) {
            wp_die( 'Security check failed.' );
        }

        if ( empty( $_FILES['effdf_csv']['tmp_name'] ) ) {
            $this->renderError( 'No file uploaded.' );
            $this->renderUploadStep();
            return;
        }

        $rows = $this->parseCsv( $_FILES['effdf_csv']['tmp_name'] );
        if ( is_wp_error( $rows ) ) {
            $this->renderError( $rows->get_error_message() );
            $this->renderUploadStep();
            return;
        }

        $headers = array_shift( $rows ); // first row = headers
        if ( empty( $headers ) ) {
            $this->renderError( 'CSV appears to be empty or has no header row.' );
            $this->renderUploadStep();
            return;
        }

        // Cache rows in a transient so we don't re-upload on execute
        $token = wp_generate_uuid4();
        set_transient( self::TRANSIENT_KEY . $token, [ 'headers' => $headers, 'rows' => $rows ], HOUR_IN_SECONDS );

        // Analyse duplicates based on title column (best guess: first column or mapped later)
        // We do a quick pre-check using column index 0 as a heuristic for display only
        $existing_titles = $this->getExistingTitles();
        $duplicate_count = 0;
        foreach ( $rows as $row ) {
            if ( isset( $row[0] ) && in_array( trim( $row[0] ), $existing_titles ) ) {
                $duplicate_count++;
            }
        }

        $total = count( $rows );
        $fields = $this->mappableFields();

        ?>
        <h2>Step 2: Map Columns &amp; Preview</h2>

        <div class="effdf-import-summary">
            <span>Total rows: <strong><?php echo $total; ?></strong></span>
            &nbsp;&bull;&nbsp;
            <span>Possible duplicates (by title, col 1): <strong><?php echo $duplicate_count; ?></strong></span>
            &nbsp;&bull;&nbsp;
            <span>New: <strong><?php echo $total - $duplicate_count; ?></strong></span>
        </div>

        <form method="post">
            <?php wp_nonce_field( self::NONCE_PREVIEW, '_wpnonce_import' ); ?>
            <input type="hidden" name="effdf_step" value="execute">
            <input type="hidden" name="effdf_token" value="<?php echo esc_attr( $token ); ?>">

            <h3>Column Mapping</h3>
            <table class="widefat effdf-mapping-table">
                <thead>
                    <tr>
                        <th>CSV Column</th>
                        <th>Sample Data</th>
                        <th>Maps To</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $headers as $i => $header ) :
                    $sample = '';
                    foreach ( $rows as $row ) {
                        if ( ! empty( $row[ $i ] ) ) { $sample = $row[ $i ]; break; }
                    }
                    $auto = $this->autoMap( $header );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $header ); ?></strong></td>
                        <td><em><?php echo esc_html( mb_strimwidth( $sample, 0, 60, '…' ) ); ?></em></td>
                        <td>
                            <select name="effdf_map[<?php echo $i; ?>]">
                                <?php foreach ( $fields as $value => $label ) : ?>
                                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $auto, $value ); ?>>
                                        <?php echo esc_html( $label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Duplicate Handling</h3>
            <fieldset>
                <label><input type="radio" name="effdf_duplicates" value="skip" checked> Skip duplicates</label><br>
                <label><input type="radio" name="effdf_duplicates" value="overwrite"> Overwrite duplicates</label><br>
                <label><input type="radio" name="effdf_duplicates" value="create"> Create new (allow duplicates)</label>
            </fieldset>

            <h3>Preview <small style="font-weight:normal;font-size:13px;">(first 20 rows)</small></h3>
            <div style="overflow-x:auto;">
            <table class="widefat effdf-preview-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php foreach ( $headers as $h ) echo '<th>' . esc_html( $h ) . '</th>'; ?>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( array_slice( $rows, 0, 20 ) as $n => $row ) :
                    $is_dup = isset( $row[0] ) && in_array( trim( $row[0] ), $existing_titles );
                    $row_class = $is_dup ? 'effdf-duplicate' : '';
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo $n + 1; ?></td>
                        <?php foreach ( $headers as $i => $_ ) : ?>
                            <td><?php echo esc_html( $row[ $i ] ?? '' ); ?></td>
                        <?php endforeach; ?>
                        <td><?php echo $is_dup ? '<span class="effdf-badge effdf-badge--dup">Duplicate</span>' : '<span class="effdf-badge effdf-badge--new">New</span>'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php if ( $total > 20 ) echo '<p><em>' . ( $total - 20 ) . ' more rows not shown.</em></p>'; ?>

            <h3>Options</h3>
            <fieldset>
                <label>
                    <input type="checkbox" name="effdf_debug" value="1">
                    Enable debug output &mdash; <em>shows per-row mapping, geocode response, and saved meta for each dealer</em>
                </label>
            </fieldset>

            <?php submit_button( 'Run Import' ); ?>
        </form>
        <?php
    }

    // -------------------------------------------------------------------------
    // STEP 3 — Execute import
    // -------------------------------------------------------------------------

    private function renderExecuteStep(): void {
        if ( ! check_admin_referer( self::NONCE_PREVIEW, '_wpnonce_import' ) ) {
            wp_die( 'Security check failed.' );
        }

        $token = sanitize_key( $_POST['effdf_token'] ?? '' );
        $data  = get_transient( self::TRANSIENT_KEY . $token );
        if ( ! $data ) {
            $this->renderError( 'Import session expired. Please start again.' );
            $this->renderUploadStep();
            return;
        }
        delete_transient( self::TRANSIENT_KEY . $token );

        $headers        = $data['headers'];
        $rows           = $data['rows'];
        $map            = $_POST['effdf_map'] ?? [];          // col_index => field_key
        $dup_handling   = sanitize_key( $_POST['effdf_duplicates'] ?? 'skip' );
        $debug          = ! empty( $_POST['effdf_debug'] );
        $existing_titles = $this->getExistingTitles(); // title => post_id

        $counts = [ 'imported' => 0, 'skipped' => 0, 'overwritten' => 0, 'geocode_failed' => 0, 'errors' => [] ];
        $debug_log = [];

        foreach ( $rows as $row_num => $row ) {
            $meta   = [];
            $title  = '';

            foreach ( $map as $col_index => $field_key ) {
                $field_key = sanitize_key( $field_key );
                $value     = trim( $row[ (int) $col_index ] ?? '' );
                if ( $field_key === '' ) continue;
                if ( $field_key === 'title' ) {
                    $title = sanitize_text_field( $value );
                } else {
                    $meta[ $field_key ] = $value;
                }
            }

            if ( $debug ) {
                $debug_log[ $row_num ] = [
                    'row'    => $row_num + 1,
                    'title'  => $title,
                    'meta'   => $meta,
                    'geo'    => null,
                    'post_id'=> null,
                    'action' => null,
                    'notes'  => [],
                ];
            }

            if ( empty( $title ) ) {
                $counts['errors'][] = "Row " . ( $row_num + 1 ) . ": no title value — skipped.";
                if ( $debug ) $debug_log[ $row_num ]['notes'][] = 'No title found — skipped.';
                $counts['skipped']++;
                continue;
            }

            $existing_id = $existing_titles[ $title ] ?? null;

            if ( $existing_id ) {
                if ( $debug ) $debug_log[ $row_num ]['notes'][] = "Duplicate found — existing post ID $existing_id. Handling: $dup_handling.";
                if ( $dup_handling === 'skip' ) {
                    $counts['skipped']++;
                    if ( $debug ) $debug_log[ $row_num ]['action'] = 'skipped (duplicate)';
                    continue;
                } elseif ( $dup_handling === 'overwrite' ) {
                    $post_id = $existing_id;
                } else {
                    $post_id = null; // create new
                }
            } else {
                if ( $debug ) $debug_log[ $row_num ]['notes'][] = 'No existing dealer found with this title — will insert.';
                $post_id = null;
            }

            // Geocode if a location field was mapped
            $location_value = $meta['dealer_location'] ?? ( $meta['dealer_address'] ?? '' );
            if ( ! empty( $location_value ) ) {
                if ( $debug ) $debug_log[ $row_num ]['notes'][] = "Geocoding: \"$location_value\"";
                $geo = $this->geocodeAddress( $location_value );
                if ( $geo['success'] ) {
                    if ( $debug ) $debug_log[ $row_num ]['geo'] = $geo;
                    $meta['dealer_latitude']    = $geo['lat'];
                    $meta['dealer_longitude']   = $geo['lng'];
                    $meta['dealer_location']    = $geo['formatted_address'];
                    $meta['dealer_address']     = $geo['street'];
                    $meta['dealer_city']        = $geo['city'];
                    $meta['dealer_state']       = $geo['state'];
                    $meta['dealer_postal_code'] = $geo['postal_code'];
                    $meta['dealer_country']     = $geo['country'];
                } else {
                    $counts['geocode_failed']++;
                    $geo_error = $geo['error'] ?? 'Unknown error';
                    $counts['errors'][] = "Row " . ( $row_num + 1 ) . " ($title) geocode failed: $geo_error";
                    if ( $debug ) {
                        $debug_log[ $row_num ]['notes'][] = 'Geocode FAILED: ' . $geo_error;
                        $debug_log[ $row_num ]['geo'] = $geo; // include raw response if present
                    }
                }
            } else {
                if ( $debug ) $debug_log[ $row_num ]['notes'][] = 'No address/location value to geocode.';
            }

            $post_args = [
                'post_title'  => $title,
                'post_type'   => 'dealer',
                'post_status' => 'publish',
            ];
            if ( $post_id ) {
                $post_args['ID'] = $post_id;
            }

            $result = wp_insert_post( $post_args, true );

            if ( is_wp_error( $result ) ) {
                $counts['errors'][] = "Row " . ( $row_num + 1 ) . " ($title): " . $result->get_error_message();
                if ( $debug ) $debug_log[ $row_num ]['notes'][] = 'wp_insert_post ERROR: ' . $result->get_error_message();
                continue;
            }

            $saved_id = $result;
            if ( $debug ) {
                $debug_log[ $row_num ]['post_id'] = $saved_id;
                $debug_log[ $row_num ]['meta']    = $meta; // updated with geocoded values
            }
            foreach ( $meta as $key => $value ) {
                update_post_meta( $saved_id, $key, sanitize_text_field( $value ) );
            }

            if ( $post_id && $dup_handling === 'overwrite' ) {
                $counts['overwritten']++;
                if ( $debug ) $debug_log[ $row_num ]['action'] = "overwritten (post ID $saved_id)";
            } else {
                $counts['imported']++;
                if ( $debug ) $debug_log[ $row_num ]['action'] = "inserted (post ID $saved_id)";
            }
        }

        // Results summary
        ?>
        <h2>Import Complete</h2>
        <table class="widefat" style="max-width:400px;">
            <tr><th>Imported (new)</th><td><?php echo $counts['imported']; ?></td></tr>
            <tr><th>Overwritten</th><td><?php echo $counts['overwritten']; ?></td></tr>
            <tr><th>Skipped (duplicates)</th><td><?php echo $counts['skipped']; ?></td></tr>
            <tr><th>Geocode failures</th><td><?php echo $counts['geocode_failed']; ?></td></tr>
        </table>

        <?php if ( ! empty( $counts['errors'] ) ) : ?>
            <h3>Row Errors</h3>
            <ul class="effdf-errors">
                <?php foreach ( $counts['errors'] as $e ) echo '<li>' . esc_html( $e ) . '</li>'; ?>
            </ul>
        <?php endif; ?>

        <?php if ( $debug && ! empty( $debug_log ) ) : ?>
            <h3>Debug Output</h3>
            <div class="effdf-debug-log">
            <?php foreach ( $debug_log as $entry ) : ?>
                <details class="effdf-debug-row">
                    <summary>
                        <strong>Row <?php echo $entry['row']; ?>:</strong>
                        <?php echo esc_html( $entry['title'] ?: '(no title)' ); ?>
                        <?php if ( $entry['action'] ) echo ' &mdash; <span class="effdf-debug-action">' . esc_html( $entry['action'] ) . '</span>'; ?>
                    </summary>
                    <?php if ( ! empty( $entry['notes'] ) ) : ?>
                        <ul class="effdf-debug-notes">
                            <?php foreach ( $entry['notes'] as $note ) echo '<li>' . esc_html( $note ) . '</li>'; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if ( ! empty( $entry['meta'] ) ) : ?>
                        <strong>Meta saved:</strong>
                        <table class="effdf-debug-table">
                            <?php foreach ( $entry['meta'] as $k => $v ) : ?>
                                <tr><td><code><?php echo esc_html( $k ); ?></code></td><td><?php echo esc_html( $v ); ?></td></tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                    <?php if ( ! empty( $entry['geo'] ) ) : ?>
                        <strong>Geocode response:</strong>
                        <table class="effdf-debug-table">
                            <?php foreach ( $entry['geo'] as $k => $v ) : ?>
                                <tr><td><code><?php echo esc_html( $k ); ?></code></td><td><?php echo esc_html( $v ); ?></td></tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </details>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=dealer' ) ); ?>" class="button">View Dealers</a>
        &nbsp;<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=dealer&page=effdf-import' ) ); ?>" class="button">Import Another File</a></p>
        <?php
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Parse a CSV file path into an array of rows */
    private function parseCsv( string $filepath ) {
        if ( ( $handle = fopen( $filepath, 'r' ) ) === false ) {
            return new \WP_Error( 'csv_open', 'Could not open uploaded file.' );
        }
        $rows = [];
        while ( ( $row = fgetcsv( $handle ) ) !== false ) {
            $rows[] = $row;
        }
        fclose( $handle );
        if ( count( $rows ) < 2 ) {
            return new \WP_Error( 'csv_empty', 'CSV must have a header row and at least one data row.' );
        }
        return $rows;
    }

    /** Get all existing dealer titles mapped to their post IDs */
    private function getExistingTitles(): array {
        $posts = get_posts([
            'post_type'      => 'dealer',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);
        $map = [];
        foreach ( $posts as $id ) {
            $map[ get_the_title( $id ) ] = $id;
        }
        return $map;
    }

    /** Attempt to auto-map a CSV header to a known field key */
    private function autoMap( string $header ): string {
        // Strip BOM, zero-width spaces, and other invisible characters, then normalise whitespace
        $h = preg_replace( '/[\x00-\x1F\x7F\xEF\xBB\xBF\xC2\xA0]/u', ' ', $header );
        $h = strtolower( trim( preg_replace( '/\s+/', ' ', $h ) ) );
        $map = [
            'name'           => 'title',
            'dealer name'    => 'title',
            'dealer'         => 'title',
            'company'        => 'title',
            'company name'   => 'title',
            'store'          => 'title',
            'store name'     => 'title',
            'title'          => 'title',
            'phone'        => 'dealer_phone',
            'fax'          => 'dealer_fax',
            'email'        => 'dealer_email',
            'website'      => 'dealer_website',
            'url'          => 'dealer_website',
            'address'      => 'dealer_location',
            'full address' => 'dealer_location',
            'location'     => 'dealer_location',
            'street'       => 'dealer_address',
            'address2'     => 'dealer_address2',
            'city'         => 'dealer_city',
            'state'        => 'dealer_state',
            'zip'          => 'dealer_postal_code',
            'postal code'  => 'dealer_postal_code',
            'postal_code'  => 'dealer_postal_code',
            'country'      => 'dealer_country',
            'lat'          => 'dealer_latitude',
            'latitude'     => 'dealer_latitude',
            'lng'          => 'dealer_longitude',
            'longitude'    => 'dealer_longitude',
        ];
        return $map[ $h ] ?? '';
    }

    /** Call Google Geocoding API. Always returns an array with 'success' key. On failure, 'error' explains why. */
    private function geocodeAddress( string $address ): array {
        // Use a dedicated server-side key if provided, otherwise fall back to the main key
        $api_key = apply_filters( 'EFFDF_GEOCODING_API_KEY', effdf_get_api_key() );
        if ( empty( $api_key ) ) {
            return [ 'success' => false, 'error' => 'No API key configured (EFFDF_GOOGLE_API_KEY filter is empty).' ];
        }

        $url = add_query_arg([
            'address' => urlencode( $address ),
            'key'     => $api_key,
        ], 'https://maps.googleapis.com/maps/api/geocode/json' );

        $response = wp_remote_get( $url, [ 'timeout' => 10 ] );
        if ( is_wp_error( $response ) ) {
            return [ 'success' => false, 'error' => 'HTTP request failed: ' . $response->get_error_message() ];
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        if ( $http_code !== 200 ) {
            return [ 'success' => false, 'error' => "HTTP $http_code returned by Geocoding API." ];
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return [ 'success' => false, 'error' => 'Could not parse JSON response: ' . json_last_error_msg() ];
        }

        $status = $body['status'] ?? 'UNKNOWN';
        if ( $status !== 'OK' ) {
            // Google error_message is only present on some statuses
            $detail = isset( $body['error_message'] ) ? ' — ' . $body['error_message'] : '';
            $hints  = [
                'ZERO_RESULTS'       => 'Address not found. Check spelling or try a more specific address.',
                'OVER_DAILY_LIMIT'   => 'API key over daily limit or billing not enabled.',
                'OVER_QUERY_LIMIT'   => 'Too many requests — query rate limit exceeded.',
                'REQUEST_DENIED'     => isset( $body['error_message'] ) && strpos( $body['error_message'], 'referer' ) !== false
                    ? 'Your API key has HTTP referrer restrictions, which block server-side requests. In Google Cloud Console → Credentials, edit the key and set Application Restrictions to "None" or "IP addresses" instead of "HTTP referrers".'
                    : 'Request denied. Verify the Geocoding API is enabled for this key in Google Cloud Console → APIs & Services → Enabled APIs.',
                'INVALID_REQUEST'    => 'Invalid request — address parameter may be missing.',
                'UNKNOWN_ERROR'      => 'Google server error — may succeed on retry.',
            ];
            $hint = isset( $hints[ $status ] ) ? ' Hint: ' . $hints[ $status ] : '';
            return [ 'success' => false, 'error' => "Google API status: $status$detail.$hint", 'status' => $status, 'raw' => $body ];
        }

        if ( empty( $body['results'][0] ) ) {
            return [ 'success' => false, 'error' => 'API returned OK but results array is empty.', 'raw' => $body ];
        }

        $result   = $body['results'][0];
        $location = $result['geometry']['location'];
        $comps    = $result['address_components'];

        $get = function( string $type, bool $short = false ) use ( $comps ): string {
            foreach ( $comps as $c ) {
                if ( in_array( $type, $c['types'] ) ) {
                    return $short ? $c['short_name'] : $c['long_name'];
                }
            }
            return '';
        };

        $street_number = $get( 'street_number' );
        $route         = $get( 'route' );
        $street        = trim( "$street_number $route" );

        return [
            'success'          => true,
            'lat'              => (string) $location['lat'],
            'lng'              => (string) $location['lng'],
            'formatted_address'=> $result['formatted_address'],
            'street'           => $street,
            'city'             => $get( 'locality' ) ?: $get( 'sublocality' ),
            'state'            => $get( 'administrative_area_level_1', true ),
            'postal_code'      => $get( 'postal_code' ),
            'country'          => $get( 'country', true ),
        ];
    }

    private function renderError( string $message ): void {
        echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>';
    }
}

/** Handle sample CSV download */
add_action( 'admin_init', function() {
    if (
        isset( $_GET['effdf_sample_csv'] ) &&
        current_user_can( 'manage_options' ) &&
        check_admin_referer( 'effdf_sample_csv' )
    ) {
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="dealer-import-sample.csv"' );
        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, [ 'Name', 'Phone', 'Fax', 'Email', 'Website', 'Address' ] );
        fputcsv( $out, [ 'Sample Dealer', '555-123-4567', '', 'dealer@example.com', 'https://example.com', '123 Main St, Springfield, IL 62701' ] );
        fclose( $out );
        exit;
    }
} );
