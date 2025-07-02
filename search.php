<?php
$documents = [
    0 => "The quick brown fox jumps",
    1 => "The fox runs fast", 
    2 => "A quick dog jumps high"
];

// Common English stop words
$stop_words = [
    'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from',
    'has', 'he', 'in', 'is', 'it', 'its', 'of', 'on', 'that', 'the',
    'to', 'was', 'will', 'with', 'but', 'or', 'so', 'i', 'you', 'we',
    'they', 'this', 'have', 'had', 'do', 'does', 'did', 'can', 'could'
];

$lookup = [];
$document_lengths = []; // Track document lengths for normalization
$term_document_frequency = []; // Track how many documents contain each term
$position_index = []; // Track term positions for phrase search

foreach($documents as $doc_k => $doc_v) {
    // Preprocessing: remove punctuation, convert to lowercase, split by whitespace
    $cleaned_text = preg_replace('/[^\w\s]/', '', strtolower($doc_v));
    $terms = preg_split('/\s+/', trim($cleaned_text));
    
    // Filter out empty strings, very short terms, and stop words
    $filtered_terms = [];
    $position = 0;
    
    foreach($terms as $term) {
        if (strlen($term) > 1 && !in_array($term, $stop_words)) {
            $filtered_terms[] = $term;
            
            // Store position information for phrase search
            if (!isset($position_index[$term][$doc_k])) {
                $position_index[$term][$doc_k] = [];
            }
            $position_index[$term][$doc_k][] = $position;
        }
        $position++;
    }
    
    // Store document length (total meaningful terms)
    $document_lengths[$doc_k] = count($filtered_terms);
    
    $term_counts = array_count_values($filtered_terms);
    
    foreach($term_counts as $term => $count) {
        $lookup[$term][$doc_k] = $count;
        
        // Count document frequency (how many documents contain this term)
        if (!isset($term_document_frequency[$term])) {
            $term_document_frequency[$term] = 0;
        }
        $term_document_frequency[$term]++;
    }
}

// Output the inverted index, document lengths, and term document frequencies
echo "Inverted Index:\n";
print_r($lookup);
echo "\nDocument Lengths:\n";
print_r($document_lengths);
echo "\nTerm Document Frequency:\n";
print_r($term_document_frequency);

// TF-IDF Scoring Functions
function calculateTFIDF($term, $doc_id, $lookup, $document_lengths, $term_document_frequency, $total_documents) {
    // Check if term exists in document
    if (!isset($lookup[$term][$doc_id])) {
        return 0;
    }
    
    // Calculate TF (Term Frequency) - normalized by document length
    $tf = $lookup[$term][$doc_id] / $document_lengths[$doc_id];
    
    // Calculate IDF (Inverse Document Frequency)
    $df = $term_document_frequency[$term];
    $idf = log($total_documents / $df);
    
    // Return TF-IDF score
    return $tf * $idf;
}

function searchDocuments($query, $lookup, $document_lengths, $term_document_frequency, $stop_words, $position_index = null) {
    $total_documents = count($document_lengths);
    
    // Check if it's a phrase query (contains quotes)
    $is_phrase_query = (strpos($query, '"') !== false);
    
    if ($is_phrase_query) {
        return searchPhrase($query, $lookup, $document_lengths, $term_document_frequency, $stop_words, $position_index);
    }
    
    // Regular term-based search
    $cleaned_query = preg_replace('/[^\w\s]/', '', strtolower($query));
    $query_terms = preg_split('/\s+/', trim($cleaned_query));
    
    // Filter query terms
    $query_terms = array_filter($query_terms, function($term) use ($stop_words) {
        return strlen($term) > 1 && !in_array($term, $stop_words);
    });
    
    $document_scores = [];
    
    // Calculate scores for each document
    foreach($document_lengths as $doc_id => $length) {
        $score = 0;
        foreach($query_terms as $term) {
            $score += calculateTFIDF($term, $doc_id, $lookup, $document_lengths, $term_document_frequency, $total_documents);
        }
        if ($score > 0) {
            $document_scores[$doc_id] = $score;
        }
    }
    
    // Sort by score (highest first)
    arsort($document_scores);
    
    return $document_scores;
}

function searchPhrase($query, $lookup, $document_lengths, $term_document_frequency, $stop_words, $position_index) {
    $total_documents = count($document_lengths);
    
    // Extract phrase from quotes
    preg_match('/"([^"]+)"/', $query, $matches);
    $phrase = $matches[1];
    
    // Preprocess phrase
    $cleaned_phrase = preg_replace('/[^\w\s]/', '', strtolower($phrase));
    $phrase_terms = preg_split('/\s+/', trim($cleaned_phrase));
    
    // Filter phrase terms
    $phrase_terms = array_filter($phrase_terms, function($term) use ($stop_words) {
        return strlen($term) > 1 && !in_array($term, $stop_words);
    });
    
    if (empty($phrase_terms)) {
        return [];
    }
    
    $document_scores = [];
    
    // Check each document for the phrase
    foreach($document_lengths as $doc_id => $length) {
        $phrase_found = false;
        
        // Get positions of first term
        $first_term = $phrase_terms[0];
        if (!isset($position_index[$first_term][$doc_id])) {
            continue;
        }
        
        // Check if phrase exists starting from each position of first term
        foreach($position_index[$first_term][$doc_id] as $start_pos) {
            $phrase_match = true;
            
            // Check if subsequent terms appear in consecutive positions
            for($i = 1; $i < count($phrase_terms); $i++) {
                $expected_pos = $start_pos + $i;
                $current_term = $phrase_terms[$i];
                
                if (!isset($position_index[$current_term][$doc_id]) || 
                    !in_array($expected_pos, $position_index[$current_term][$doc_id])) {
                    $phrase_match = false;
                    break;
                }
            }
            
            if ($phrase_match) {
                $phrase_found = true;
                break;
            }
        }
        
        if ($phrase_found) {
            // Calculate score based on phrase length and document characteristics
            $phrase_score = 0;
            foreach($phrase_terms as $term) {
                $phrase_score += calculateTFIDF($term, $doc_id, $lookup, $document_lengths, $term_document_frequency, $total_documents);
            }
            // Boost score for exact phrase match
            $document_scores[$doc_id] = $phrase_score * 1.5;
        }
    }
    
    // Sort by score (highest first)
    arsort($document_scores);
    
    return $document_scores;
}

// Example search
echo "\n\nExample Search for 'quick fox':\n";
$search_results = searchDocuments('quick fox', $lookup, $document_lengths, $term_document_frequency, $stop_words, $position_index);
foreach($search_results as $doc_id => $score) {
    echo "Document $doc_id: " . $documents[$doc_id] . " (Score: " . round($score, 4) . ")\n";
}

echo "\n\nExample Phrase Search for '\"quick brown\"':\n";
$phrase_results = searchDocuments('"quick brown"', $lookup, $document_lengths, $term_document_frequency, $stop_words, $position_index);
if (empty($phrase_results)) {
    echo "No documents contain the exact phrase 'quick brown'\n";
} else {
    foreach($phrase_results as $doc_id => $score) {
        echo "Document $doc_id: " . $documents[$doc_id] . " (Score: " . round($score, 4) . ")\n";
    }
}

echo "\n\nExample Phrase Search for '\"brown fox\"':\n";
$phrase_results2 = searchDocuments('"brown fox"', $lookup, $document_lengths, $term_document_frequency, $stop_words, $position_index);
if (empty($phrase_results2)) {
    echo "No documents contain the exact phrase 'brown fox'\n";
} else {
    foreach($phrase_results2 as $doc_id => $score) {
        echo "Document $doc_id: " . $documents[$doc_id] . " (Score: " . round($score, 4) . ")\n";
    }
}
?>
