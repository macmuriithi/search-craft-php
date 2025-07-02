# 🔍 SearchCraft
> *A Miniature Search Engine Implementation in PHP*

**SearchCraft** is an educational implementation of a complete text search engine that demonstrates fundamental information retrieval concepts. Built from scratch in PHP, it showcases how modern search engines work under the hood, from document indexing to relevance scoring.

## 🌟 Why SearchCraft?

Understanding how search engines work is crucial for developers, data scientists, and anyone working with text data. SearchCraft provides a hands-on, transparent implementation that you can study, modify, and extend to learn core concepts like TF-IDF scoring, inverted indexing, and phrase matching.

## ⚡ Key Features

### ✅ **Inverted Index Construction**
The foundation of any search engine - creates a reverse mapping from terms to documents for lightning-fast lookups.

**How it works:**
- Documents are preprocessed (lowercase, punctuation removal)
- Each unique term is mapped to all documents containing it
- Term frequencies are tracked for each document

### ✅ **Advanced Text Preprocessing**
Smart text cleaning that prepares documents for optimal search performance.

**Implementation includes:**
- **Punctuation removal**: Strips non-alphanumeric characters
- **Case normalization**: Converts everything to lowercase for consistent matching  
- **Tokenization**: Splits text into individual searchable terms
- **Empty term filtering**: Removes meaningless tokens

### ✅ **Intelligent Stop Word Filtering**
Eliminates common words that don't contribute to search relevance.

**Features:**
- Comprehensive stop word list (articles, prepositions, common verbs)
- Configurable stop word dictionary
- Improves search precision by focusing on meaningful terms

### ✅ **Document Length Normalization**
Prevents longer documents from dominating search results unfairly.

**How it helps:**
- Tracks total meaningful terms per document
- Normalizes term frequency by document length
- Ensures fair comparison between short and long documents

### ✅ **Term Document Frequency (DF) Tracking**
Measures how common or rare each term is across the entire collection.

**Purpose:**
- Counts how many documents contain each term
- Essential for calculating IDF (Inverse Document Frequency)
- Helps identify discriminative vs. common terms

### ✅ **Full TF-IDF Scoring Implementation**
The gold standard for text relevance ranking in information retrieval.

**TF-IDF Formula:**
```
TF-IDF = (Term Frequency / Document Length) × log(Total Documents / Documents Containing Term)
```

**Components:**
- **TF (Term Frequency)**: How often a term appears in a document (normalized)
- **IDF (Inverse Document Frequency)**: How rare/unique a term is across all documents
- **Combined Score**: Higher scores for terms that are frequent in a document but rare overall

### ✅ **Multi-term Query Support**
Handles complex queries with multiple search terms.

**Features:**
- Processes queries with multiple keywords
- Combines TF-IDF scores across all query terms
- Ranks documents by total relevance score

### ✅ **Exact Phrase Search with Position Matching**
Advanced feature that finds exact word sequences within documents.

**How it works:**
- **Position indexing**: Records where each term appears in documents
- **Phrase detection**: Identifies quoted strings in queries
- **Sequential matching**: Verifies terms appear in exact order
- **Boosted scoring**: Phrase matches receive higher relevance scores

### ✅ **Relevance-based Ranking**
Sophisticated sorting that presents the most relevant results first.

**Ranking features:**
- Documents sorted by descending TF-IDF scores
- Phrase matches receive score multipliers
- Zero-score results are filtered out

## 🚀 Getting Started

### Prerequisites
- PHP 7.0 or higher
- Basic understanding of arrays and string manipulation

### Quick Start

1. **Clone the repository:**
```bash
git clone https://github.com/yourusername/searchcraft.git
cd searchcraft
```

2. **Run the example:**
```bash
php searchcraft.php
```

3. **Expected output:**
```
Inverted Index:
Array
(
    [quick] => Array
        (
            [0] => 1
            [2] => 1
        )
    [brown] => Array
        (
            [0] => 1
        )
    // ... more terms
)

Example Search for 'quick fox':
Document 0: The quick brown fox jumps (Score: 0.4055)
Document 1: The fox runs fast (Score: 0.1823)
```

## 📚 Core Concepts Explained

### Inverted Index
Think of it like a book's index, but reversed. Instead of "Page 50: Search Engines", it's "Search Engines: appears on pages 15, 23, 50". This allows instant lookup of which documents contain any given term.

### TF-IDF Intuition
Imagine searching for "Python programming":
- **High TF**: A document mentioning "Python" 10 times is more relevant than one mentioning it once
- **High IDF**: "Python" is more valuable than "the" because it's rarer and more descriptive
- **Combined**: Documents with frequent mentions of rare, descriptive terms rank highest

### Phrase Search Challenges
Finding "New York" is harder than finding "New" and "York" separately. The system must:
1. Locate documents containing both terms
2. Verify they appear consecutively
3. Handle multiple occurrences efficiently

## 🔧 Usage Examples

### Basic Term Search
```php
$results = searchDocuments('quick fox', $lookup, $document_lengths, 
                          $term_document_frequency, $stop_words, $position_index);
```

### Phrase Search
```php
$results = searchDocuments('"brown fox"', $lookup, $document_lengths, 
                          $term_document_frequency, $stop_words, $position_index);
```

### Custom Document Collection
```php
$documents = [
    0 => "Your first document text here",
    1 => "Your second document text here",
    2 => "Your third document text here"
];
// Run the indexing process...
```

## 🎓 Learning Objectives

After studying SearchCraft, you'll understand:

- **Information Retrieval Fundamentals**: How search engines organize and retrieve information
- **Text Processing Pipelines**: Real-world techniques for cleaning and preparing text data
- **Relevance Scoring**: Mathematical approaches to ranking search results
- **Index Structures**: Efficient data structures for fast text search
- **Query Processing**: How different query types are parsed and executed

## 🛠 Extending SearchCraft

### Add New Features
- **Fuzzy matching**: Handle typos and spelling variations
- **Stemming**: Reduce words to root forms (running → run)
- **Boolean operators**: Support AND, OR, NOT queries
- **Weighted fields**: Give titles more importance than body text
- **Result snippets**: Generate preview text with highlighted terms

### Performance Optimizations
- **Persistent storage**: Save indexes to files/databases
- **Memory optimization**: Handle larger document collections
- **Parallel processing**: Index multiple documents simultaneously
- **Caching**: Store frequent query results

## 📖 Educational Use Cases

### Computer Science Courses
- **Data Structures**: Demonstrate hash tables and inverted indexes
- **Algorithms**: Show text processing and ranking algorithms
- **Information Retrieval**: Hands-on IR system implementation

### Self-Learning Projects
- **Modify and experiment**: Change scoring formulas and see results
- **Add your own data**: Index your own document collections
- **Compare approaches**: Implement alternative ranking methods

### Portfolio Projects
- **Extend functionality**: Build web interface or API
- **Scale up**: Handle larger datasets
- **Integrate**: Combine with databases or web frameworks

## 🤝 Contributing

We welcome contributions that improve SearchCraft's educational value:

1. **Documentation improvements**: Clarify explanations or add examples
2. **Code enhancements**: Optimize performance or add features  
3. **Educational content**: Create tutorials or lesson plans
4. **Bug fixes**: Report and fix issues

### Contribution Guidelines
- Keep code readable and well-commented
- Maintain educational focus over performance
- Include examples for new features
- Update documentation for changes

## 📝 License

MIT License - feel free to use SearchCraft for educational purposes, personal projects, or as a foundation for more complex systems.

## 🙏 Acknowledgments

SearchCraft is inspired by classic information retrieval textbooks and real-world search engine implementations. It distills complex concepts into understandable, hands-on code that bridges theory and practice.

## 📞 Support

- **Issues**: Report bugs or request features via GitHub Issues
- **Discussions**: Ask questions or share improvements in GitHub Discussions
- **Documentation**: Check the inline code comments for detailed explanations

---

*Happy searching! 🔍*
