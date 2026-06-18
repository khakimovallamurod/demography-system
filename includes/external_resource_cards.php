<?php if (!empty($external_resources)): ?>
<div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4 items-stretch">
    <?php foreach ($external_resources as $resource): ?>
    <section class="rounded-3xl border <?= h($resource['accent']['ring'] ?? 'border-gray-100') ?> bg-white shadow-sm overflow-hidden flex flex-col">
        <div class="bg-gradient-to-br <?= h($resource['accent']['shell'] ?? 'from-gray-500 to-gray-700') ?> p-[1px] flex-1 flex flex-col">
            <div class="bg-white rounded-[23px] flex-1 flex flex-col">
                <div class="bg-gradient-to-br <?= h($resource['accent']['soft'] ?? 'from-gray-50 to-white') ?> p-4 border-b border-white/70">
                    <div class="flex flex-col gap-2 relative">
                        <div class="flex items-start gap-3 min-w-0 pr-16">
                            <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center <?= h($resource['accent']['icon'] ?? 'bg-gray-100 text-gray-700') ?>">
                                <i class="fas <?= $resource['site_key'] === 'stat_uz' ? 'fa-chart-column' : ($resource['site_key'] === 'siat' ? 'fa-chart-pie' : ($resource['site_key'] === 'democalc' ? 'fa-calculator' : 'fa-earth-asia')) ?> text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 truncate"><?= h($resource['site_name'] ?? '') ?></p>
                                <h3 class="text-sm font-bold text-gray-800 leading-tight line-clamp-2 mt-0.5" title="<?= h($resource['title'] ?? '') ?>"><?= h($resource['title'] ?? '') ?></h3>
                            </div>
                        </div>
                        <span class="absolute top-0 right-0 text-[10px] font-semibold px-2 py-0.5 rounded-lg whitespace-nowrap <?= h($resource['accent']['badge'] ?? 'bg-gray-100 text-gray-700') ?>">
                            <?= h($resource['status'] ?? '') ?>
                        </span>
                    </div>
                    <p class="text-[13px] text-gray-500 mt-3 leading-relaxed line-clamp-2" title="<?= h($resource['summary'] ?? '') ?>"><?= h($resource['summary'] ?? '') ?></p>
                </div>

                <div class="p-4 space-y-2 flex-1">
                    <?php foreach (($resource['items'] ?? []) as $item): ?>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 px-3 py-2 flex flex-col justify-center">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 truncate mb-0.5"><?= h($item['label'] ?? '') ?></span>
                        <p class="text-[13px] font-bold text-gray-700 leading-tight truncate"><?= h($item['value'] ?? '') ?></p>
                        <?php if (!empty($item['note'])): ?>
                        <p class="text-[11px] text-gray-400 mt-0.5 truncate" title="<?= h($item['note']) ?>"><?= h($item['note']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="px-4 pb-4 pt-2 flex items-center justify-between mt-auto border-t border-gray-50">
                    <p class="text-[11px] text-gray-400">
                        <?= h($resource['updated_at'] ?? '') ?>
                    </p>
                    <a href="<?= h($resource['url'] ?? '#') ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition hover:-translate-y-0.5 hover:shadow-md <?= h($resource['accent']['button'] ?? 'bg-gray-700 hover:bg-gray-800') ?>">
                        Batafsil
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
</div>
<?php endif; ?>
