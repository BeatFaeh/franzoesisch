<?php

declare(strict_types=1);

$mode = in_array($_GET['modus'] ?? 'text', ['text', 'mc'], true)
    ? $_GET['modus']
    : 'text';

$direction = ($_GET['richtung'] ?? 'de-fr') === 'fr-de'
    ? 'fr-de'
    : 'de-fr';

$topicInput = $_GET['thema'] ?? [];

$selectedTopics = is_array($topicInput)
    ? $topicInput
    : [$topicInput];

$selectedTopics = array_values(
    array_unique(
        array_filter(
            array_map(
                static fn($v): string => trim((string) $v),
                $selectedTopics
            ),
            static fn(string $v): bool => $v !== ''
        )
    )
);

$search = trim((string) ($_GET['suche'] ?? ''));

$results = [];
$msg = '';

if ($search !== '') {
    if (ctype_digit($search)) {
        $card = $cardRepository->findById(
            (int) $search,
            $direction
        );
    } else {
        $results = $cardRepository->searchByTerm(
            $search,
            $direction,
            $selectedTopics
        );

        $card = $results[0] ?? null;
    }

    if (!$card) {
        $msg = 'Keine passende Lernkarte gefunden.';
    }
} else {
    $card = $cardRepository->random(
        $direction,
        $selectedTopics
    );
}

$count = $cardRepository->count(
    $direction,
    $selectedTopics
);

$topics = $cardRepository->topics($direction);

$quiz = [
    'options'     => [],
    'correct_key' => '',
];

if ($mode === 'mc' && $card) {
    $quiz = $quizService->build(
        $card,
        $direction,
        $selectedTopics
    );
}

$params = http_build_query([
    'richtung' => $direction,
    'thema'    => $selectedTopics,
]);

?>

<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Französische Lernkarten</title>

    <link
        rel="stylesheet"
        href="assets/css/main.css"
    >
</head>

<body>

<main class="page">
    <div class="wrapper">

        <?php require __DIR__ . '/../partials/public-hero.php'; ?>

        <section class="control-card">

            <form
                method="get"
                action="index.php"
                class="filters"
            >

                <label>
                    Richtung

                    <select name="richtung">
                        <option
                            value="de-fr"
                            <?= $direction === 'de-fr' ? 'selected' : '' ?>
                        >
                            Deutsch → Französisch
                        </option>

                        <option
                            value="fr-de"
                            <?= $direction === 'fr-de' ? 'selected' : '' ?>
                        >
                            Französisch → Deutsch
                        </option>
                    </select>
                </label>

                <label class="lesson-filter">
                    Thema/Themen

                    <div class="lesson-multiselect">

                        <label class="lesson-option lesson-all">
                            <input
                                type="checkbox"
                                id="topic-all"
                                <?= $selectedTopics === [] ? 'checked' : '' ?>
                            >

                            <span>Alle Themen</span>
                        </label>

                        <?php foreach ($topics as $topic): ?>

                            <label class="lesson-option">
                                <input
                                    type="checkbox"
                                    name="thema[]"
                                    value="<?= Html::e($topic) ?>"
                                    <?= in_array($topic, $selectedTopics, true)
                                        ? 'checked'
                                        : '' ?>
                                >

                                <span><?= Html::e($topic) ?></span>
                            </label>

                        <?php endforeach; ?>

                    </div>
                </label>

                <label class="search-grow">
                    Wort, Thema oder ID suchen

                    <input
                        type="search"
                        name="suche"
                        value="<?= Html::e($search) ?>"
                        placeholder="z. B. Name, nom oder 12"
                    >
                </label>

                <button class="button button-primary">
                    Anzeigen
                </button>

            </form>

            <nav class="mode-switch">

                <form
                    method="get"
                    action="index.php"
                    class="mode-form"
                >

                    <input
                        type="hidden"
                        name="richtung"
                        value="<?= Html::e($direction) ?>"
                    >

                    <?php foreach ($selectedTopics as $selectedTopic): ?>

                        <input
                            type="hidden"
                            name="thema[]"
                            value="<?= Html::e($selectedTopic) ?>"
                        >

                    <?php endforeach; ?>

                    <button
                        type="submit"
                        name="modus"
                        value="text"
                        class="mode-button <?= $mode === 'text' ? 'active' : '' ?>"
                    >
                        📖 Lernkarte
                    </button>

                    <button
                        type="submit"
                        name="modus"
                        value="mc"
                        class="mode-button <?= $mode === 'mc' ? 'active' : '' ?>"
                    >
                        ✓ Multiple Choice
                    </button>

                </form>

            </nav>

        </section>

        <?php if ($msg): ?>

            <div class="search-message">
                <?= Html::e($msg) ?>
            </div>

        <?php endif; ?>

        <?php if ($card): ?>

            <section class="learning-card">

                <div class="card-meta">

                    <span class="badge">
                        Lektion <?= Html::e((string) $card['lektion']) ?>
                    </span>

                    <span class="badge">
                        DB-ID #<?= (int) $card['id'] ?>
                    </span>

                    <span class="badge">
                        <?= Html::e(
                            $direction === 'de-fr'
                                ? (string) $card['thema_d']
                                : (string) $card['thema_f']
                        ) ?>
                    </span>

                </div>

                <div class="card-content">

                    <p class="question-label">
                        <?= $direction === 'de-fr'
                            ? 'Deutsch'
                            : 'Französisch' ?>
                    </p>

                    <h2>
                        <?= Html::e((string) $card['frage']) ?>
                    </h2>

                    <?php if ($mode === 'text'): ?>

                        <details class="answer">

                            <summary>
                                Antwort anzeigen
                            </summary>

                            <div class="answer-text">
                                <?= Html::e((string) $card['antwort']) ?>
                            </div>

                        </details>

                    <?php elseif (count($quiz['options']) === 4): ?>

                        <div
                            class="quiz-options"
                            data-correct="<?= Html::e($quiz['correct_key']) ?>"
                        >

                            <?php foreach ($quiz['options'] as $k => $v): ?>

                                <button
                                    type="button"
                                    class="quiz-option"
                                    data-key="<?= $k ?>"
                                >
                                    <strong><?= $k ?></strong>

                                    <span class="quiz-option-text">
                                        <?= Html::e($v) ?>
                                    </span>
                                </button>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        <?php endif; ?>

        <div class="card-actions">

            <a
                class="button button-primary"
                href="index.php?modus=<?= Html::e($mode) ?>&<?= $params ?>"
            >
                Zufällige Lernkarte
            </a>

            <a
                class="button button-secondary"
                href="index.php?action=pruefung"
            >
                Prüfung
            </a>

            <a
                class="button button-secondary"
                href="index.php?action=admin"
            >
                Administration
            </a>

        </div>

        <p class="muted">
            <?= $count ?> Lernkarten in der aktuellen Auswahl.
        </p>

    </div>
</main>

<?php require __DIR__ . '/../partials/site-footer.php'; ?>	
	
<script src="assets/js/quiz.js"></script>

<script>
    document
        .getElementById('topic-all')
        ?.addEventListener('change', e => {
            if (e.target.checked) {
                document
                    .querySelectorAll('input[name="thema[]"]')
                    .forEach(x => x.checked = false);
            }
        });
</script>

</body>
</html>