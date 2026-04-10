<?php if (!empty($external_resources)): ?>
<div class="grid xl:grid-cols-2 gap-4">
    <?php foreach ($external_resources as $resource): ?>
    <section class="rounded-[28px] border <?= h($resource['accent']['ring'] ?? 'border-gray-100') ?> bg-white shadow-sm overflow-hidden">
        <div class="bg-gradient-to-br <?= h($resource['accent']['shell'] ?? 'from-gray-500 to-gray-700') ?> p-[1px]">
            <div class="bg-white rounded-[27px]">
                <div class="bg-gradient-to-br <?= h($resource['accent']['soft'] ?? 'from-gray-50 to-white') ?> px-4 py-5 sm:px-5 border-b border-white/70">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center <?= h($resource['accent']['icon'] ?? 'bg-gray-100 text-gray-700') ?>">
                                <i class="fas <?= $resource['site_key'] === 'stat_uz' ? 'fa-chart-column' : 'fa-earth-asia' ?> text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 break-words"><?= h($resource['site_name'] ?? '') ?></p>
                                <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight break-words"><?= h($resource['title'] ?? '') ?></h3>
                            </div>
                        </div>
                        <span class="w-fit text-[11px] font-semibold px-2.5 py-1 rounded-full sm:whitespace-nowrap <?= h($resource['accent']['badge'] ?? 'bg-gray-100 text-gray-700') ?>">
                            <?= h($resource['status'] ?? '') ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-3 leading-6 break-words"><?= h($resource['summary'] ?? '') ?></p>
                </div>

                <div class="px-4 py-4 space-y-3 sm:px-5">
                    <?php foreach (($resource['items'] ?? []) as $item): ?>
                    <div class="rounded-2xl border border-gray-100 bg-white px-4 py-3">
                        <div class="flex items-center justify-between gap-3 mb-1">
                            <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-400 break-words"><?= h($item['label'] ?? '') ?></span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 leading-6 break-words"><?= h($item['value'] ?? '') ?></p>
                        <?php if (!empty($item['note'])): ?>
                        <p class="text-xs text-gray-500 mt-1 leading-5 break-words"><?= h($item['note']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="px-4 pb-5 flex flex-col items-start gap-3 sm:px-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-gray-400 break-words">
                        Yangilangan: <?= h($resource['updated_at'] ?? '') ?>
                    </p>
                    <a href="<?= h($resource['url'] ?? '#') ?>" target="_blank" rel="noopener"
                       class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition <?= h($resource['accent']['button'] ?? 'bg-gray-700 hover:bg-gray-800') ?>">
                        Batafsil
                        <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
</div>
<?php endif; ?>
