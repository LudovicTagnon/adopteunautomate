use use Symfony\Component\Form\DataTransformerInterface;

class DateTimetoDTImmuTransfo implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transforme la DateTimeImmutable en DateTime pour l'affichage dans le formulaire
        return (DateTime) $value;
    }

    public function reverseTransform($value)
    {
        // Transforme la DateTime en DateTimeImmutable pour l'injection dans l'entité
        return (DateTimeImmutable) $value;
    }
}